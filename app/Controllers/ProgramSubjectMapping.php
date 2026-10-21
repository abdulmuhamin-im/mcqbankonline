<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class ProgramSubjectMapping extends BaseController
{
    private function allowed(): bool
    {
        return (bool) session()->get('isLoggedIn')
            && in_array((string) session()->get('role'), ['0', '1'], true);
    }

    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->allowed()) {
            return redirect()->to(site_url('dashboard/member'))->with('error', 'You are not authorized to manage program-subject mappings.');
        }

        $db = db_connect();
        $programs = $db->table('mcq_tbl_programs')->orderBy('progName', 'ASC')->get()->getResultArray();
        $selectedProgram = (int) ($this->request->getGet('progId') ?? $this->request->getPost('progId') ?? 0);
        if ($selectedProgram === 0 && $programs) {
            $selectedProgram = (int) $programs[0]['progId'];
        }
        $programExists = false;
        foreach ($programs as $program) {
            if ((int) $program['progId'] === $selectedProgram) $programExists = true;
        }
        if (! $programExists) $selectedProgram = 0;

        $subjectsByDepartment = [];
        $allowedDepartments = [];
        $selectedSubjects = [];
        if ($selectedProgram > 0) {
            // Only departments already mapped to this program can be used here.
            $allowedDepartments = $db->table('mcq_tbl_program_department pd')
                ->select('d.department_id, d.department_name')
                ->join('mcq_tbl_departments d', 'd.department_id = pd.department_id')
                ->where('pd.progId', $selectedProgram)
                ->where('pd.is_active', 1)
                ->orderBy('d.department_name', 'ASC')->get()->getResultArray();

            foreach ($allowedDepartments as $department) {
                $subjectsByDepartment[(int) $department['department_id']] = $db->table('mcq_tbl_subject')
                    ->where('department_id', (int) $department['department_id'])
                    ->orderBy('subject_name', 'ASC')->get()->getResultArray();
            }
            $selectedSubjects = array_map('intval', array_column(
                $db->table('mcq_tbl_program_subject')->select('subject_id')->where('progId', $selectedProgram)->where('is_active', 1)->get()->getResultArray(),
                'subject_id'
            ));
        }

        return view('program_subject_mapping/index', [
            'title' => 'Program–Subject Mapping',
            'programs' => $programs,
            'selectedProgram' => $selectedProgram,
            'allowedDepartments' => $allowedDepartments,
            'subjectsByDepartment' => $subjectsByDepartment,
            'selectedSubjects' => $selectedSubjects,
        ]);
    }

    public function save()
    {
        if (! session()->get('isLoggedIn')) return redirect()->to(site_url('login'));
        if (! $this->allowed()) return redirect()->to(site_url('dashboard/member'))->with('error', 'You are not authorized to manage mappings.');

        $db = db_connect();
        $programId = (int) $this->request->getPost('progId');
        $program = $db->table('mcq_tbl_programs')->where('progId', $programId)->get()->getRowArray();
        if (! $program) return redirect()->to(site_url('master/program-subjects'))->with('error', 'Please select a valid program.');

        $allowed = $db->table('mcq_tbl_program_department')->select('department_id')->where('progId', $programId)->where('is_active', 1)->get()->getResultArray();
        $allowedDepartmentIds = array_map('intval', array_column($allowed, 'department_id'));
        $submittedIds = $this->request->getPost('subject_ids');
        if (! is_array($submittedIds)) $submittedIds = [];
        $submittedIds = array_values(array_unique(array_filter(array_map('intval', $submittedIds), static fn($id) => $id > 0)));

        // Reject any subject not belonging to a department currently mapped to the selected program.
        $validIds = [];
        if ($submittedIds) {
            $rows = $db->table('mcq_tbl_subject')->select('subject_id, department_id')->whereIn('subject_id', $submittedIds)->get()->getResultArray();
            foreach ($rows as $row) {
                if (in_array((int) $row['department_id'], $allowedDepartmentIds, true)) $validIds[] = (int) $row['subject_id'];
            }
            if (count($validIds) !== count($submittedIds)) {
                return redirect()->to(site_url('master/program-subjects?progId=' . $programId))
                    ->with('error', 'Some selected subjects do not belong to a department mapped to this program. No changes were saved.');
            }
        }

        $db->transStart();
        // Deactivate instead of deleting, so mapping history/records are preserved.
        $db->table('mcq_tbl_program_subject')->where('progId', $programId)->update(['is_active' => 0]);
        foreach ($validIds as $subjectId) {
            $existing = $db->table('mcq_tbl_program_subject')->where('progId', $programId)->where('subject_id', $subjectId)->get()->getRowArray();
            if ($existing) {
                $db->table('mcq_tbl_program_subject')->where('mapping_id', (int) $existing['mapping_id'])->update(['is_active' => 1]);
            } else {
                $db->table('mcq_tbl_program_subject')->insert([
                    'progId' => $programId,
                    'subject_id' => $subjectId,
                    'is_active' => 1,
                ]);
            }
        }
        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->to(site_url('master/program-subjects?progId=' . $programId))->with('error', 'Save failed. Please check the database structure and try again.');
        }
        return redirect()->to(site_url('master/program-subjects?progId=' . $programId))->with('success', 'Program–Subject mapping saved successfully.');
    }
}
