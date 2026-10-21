<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Programs extends BaseController
{
    private function canManage(): bool
    {
        return (bool) session()->get('isLoggedIn')
            && in_array((string) session()->get('role'), ['0', '1'], true);
    }

    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->canManage()) {
            return redirect()->to(site_url('dashboard/member'))->with('error', 'Only administrators can manage programs.');
        }

        $db = db_connect();
        $error = null;

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = trim((string) $this->request->getPost('action'));
            $programId = filter_var($this->request->getPost('progId'), FILTER_VALIDATE_INT);

            if ($action === 'save') {
                $name = trim((string) $this->request->getPost('progName'));
                if ($name === '' || mb_strlen($name) > 150) {
                    $error = 'Program name is required and must not exceed 150 characters.';
                } else {
                    $duplicate = $db->table('mcq_tbl_programs')->where('progName', $name);
                    if ($programId) {
                        $duplicate->where('progId !=', $programId);
                    }
                    if ($duplicate->countAllResults() > 0) {
                        $error = 'A program with this name already exists.';
                    } elseif ($programId) {
                        $exists = $db->table('mcq_tbl_programs')->where('progId', $programId)->countAllResults() > 0;
                        if (! $exists) {
                            $error = 'The selected program was not found. Refresh the page and try again.';
                        } else {
                            $ok = $db->table('mcq_tbl_programs')->where('progId', $programId)->update(['progName' => $name]);
                            if ($ok) return redirect()->to(site_url('master/programs'))->with('success', 'Program updated successfully.');
                            $error = 'Program could not be updated. Check the application error log.';
                        }
                    } else {
                        $ok = $db->table('mcq_tbl_programs')->insert(['progName' => $name, 'is_Active' => 1]);
                        if ($ok) return redirect()->to(site_url('master/programs'))->with('success', 'Program added successfully.');
                        $error = 'Program could not be saved. Check the application error log.';
                    }
                }
            } elseif ($action === 'toggle') {
                if (! $programId || $db->table('mcq_tbl_programs')->where('progId', $programId)->countAllResults() === 0) {
                    $error = 'The selected program was not found.';
                } else {
                    $program = $db->table('mcq_tbl_programs')->select('is_Active')->where('progId', $programId)->get()->getRowArray();
                    $newStatus = ((int) ($program['is_Active'] ?? 0) === 1) ? 0 : 1;
                    if ($db->table('mcq_tbl_programs')->where('progId', $programId)->update(['is_Active' => $newStatus])) {
                        return redirect()->to(site_url('master/programs'))->with('success', $newStatus ? 'Program activated successfully.' : 'Program deactivated successfully. Existing records and mappings were preserved.');
                    }
                    $error = 'Program status could not be changed.';
                }
            } else {
                $error = 'Invalid request.';
            }
        }

        $records = $db->table('mcq_tbl_programs')
            ->select('progId, progName, is_Active')
            ->orderBy('progId', 'ASC')->get()->getResultArray();

        return view('programs/index', [
            'records' => $records,
            'error' => $error,
            'success' => session()->getFlashdata('success'),
        ]);
    }
}
