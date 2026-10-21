<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class ProgramDepartmentMapping extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (! in_array((string) session()->get('role'), ['0', '1'], true)) {
            return redirect()->to(site_url('dashboard/member'))->with('error', 'Only administrators can access this page.');
        }

        $db = db_connect();
        $error = null;
        $selectedProgramId = (int) ($this->request->getGet('program_id') ?? $this->request->getPost('progId') ?? 0);

        try {
            $programs = $db->table('mcq_tbl_programs')
                ->select('progId, progName')
                ->orderBy('progName', 'ASC')->get()->getResultArray();
            $departments = $db->table('mcq_tbl_departments')
                ->select('department_id, department_name')
                ->orderBy('department_name', 'ASC')->get()->getResultArray();

            if (strtolower($this->request->getMethod()) === 'post') {
                $postedProgramId = filter_var($this->request->getPost('progId'), FILTER_VALIDATE_INT);
                $postedDepartments = $this->request->getPost('departments');
                $postedDepartments = is_array($postedDepartments) ? $postedDepartments : [];

                if (!$postedProgramId || $db->table('mcq_tbl_programs')->where('progId', (int) $postedProgramId)->countAllResults() === 0) {
                    $error = 'Please select a valid Program.';
                } else {
                    $validDepartmentIds = [];
                    foreach ($postedDepartments as $departmentId) {
                        $id = filter_var($departmentId, FILTER_VALIDATE_INT);
                        if ($id && $db->table('mcq_tbl_departments')->where('department_id', (int) $id)->countAllResults() > 0) {
                            $validDepartmentIds[] = (int) $id;
                        }
                    }
                    $validDepartmentIds = array_values(array_unique($validDepartmentIds));

                    try {
                        $db->transBegin();
                        $existing = $db->table('mcq_tbl_program_department')
                            ->select('mapping_id, department_id')
                            ->where('progId', (int) $postedProgramId)->get()->getResultArray();
                        $existingByDepartment = [];
                        foreach ($existing as $row) {
                            $existingByDepartment[(int) $row['department_id']] = (int) $row['mapping_id'];
                        }

                        // Keep mapping rows: unchecked associations are deactivated, not deleted.
                        $db->table('mcq_tbl_program_department')
                            ->where('progId', (int) $postedProgramId)
                            ->update(['is_active' => 0]);

                        foreach ($validDepartmentIds as $departmentId) {
                            if (isset($existingByDepartment[$departmentId])) {
                                $db->table('mcq_tbl_program_department')
                                    ->where('mapping_id', $existingByDepartment[$departmentId])
                                    ->update(['is_active' => 1]);
                            } else {
                                $db->table('mcq_tbl_program_department')->insert([
                                    'progId' => (int) $postedProgramId,
                                    'department_id' => $departmentId,
                                    'is_active' => 1,
                                ]);
                            }
                        }

                        if ($db->transStatus() === false) {
                            $db->transRollback();
                            $error = 'The mapping could not be saved. No changes were committed.';
                        } else {
                            $db->transCommit();
                            return redirect()->to(site_url('master/program-departments?program_id=' . (int) $postedProgramId))
                                ->with('success', 'Department mapping saved successfully.');
                        }
                    } catch (DatabaseException $e) {
                        $db->transRollback();
                        log_message('error', 'Program-department mapping save failed: {message}', ['message' => $e->getMessage()]);
                        $error = 'The mapping could not be saved. Please confirm that the mapping table has been created and check the application log.';
                    }
                }
                $selectedProgramId = (int) ($postedProgramId ?: 0);
            }

            $activeDepartmentIds = [];
            if ($selectedProgramId > 0) {
                $activeRows = $db->table('mcq_tbl_program_department')
                    ->select('department_id')->where('progId', $selectedProgramId)->where('is_active', 1)->get()->getResultArray();
                $activeDepartmentIds = array_map(static fn ($row) => (int) $row['department_id'], $activeRows);
            }
        } catch (DatabaseException $e) {
            log_message('error', 'Program-department mapping page failed: {message}', ['message' => $e->getMessage()]);
            $programs = $programs ?? [];
            $departments = $departments ?? [];
            $activeDepartmentIds = [];
            $error = 'The page could not load its data. Please check the Programs, Departments and Program–Department Mapping tables.';
        }

        return view('master_data/program_department_mapping', [
            'pageTitle' => 'Program–Department Mapping',
            'programs' => $programs,
            'departments' => $departments,
            'selectedProgramId' => $selectedProgramId,
            'activeDepartmentIds' => $activeDepartmentIds ?? [],
            'error' => $error,
            'success' => session()->getFlashdata('success'),
        ]);
    }
}
