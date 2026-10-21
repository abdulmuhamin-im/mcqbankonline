<?php

namespace App\Controllers;

class Teachers extends BaseController
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
            return redirect()->to(site_url('dashboard/member'))->with('error', 'Only administrators can manage teachers.');
        }

        $db = db_connect();
        $error = null;
        $subjects = $db->table('mcq_tbl_subject')->select('subject_id, subject_name, department_id')->orderBy('subject_name', 'ASC')->get()->getResultArray();
        $departments = $db->table('mcq_tbl_departments')->select('department_id, department_name')->orderBy('department_name', 'ASC')->get()->getResultArray();
        $designations = $db->table('mcq_tbl_designations')->select('designation_id, designation_name')->orderBy('designation_name', 'ASC')->get()->getResultArray();

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = trim((string) $this->request->getPost('action'));
            $id = filter_var($this->request->getPost('author_id'), FILTER_VALIDATE_INT);

            if ($action === 'save') {
                $name = trim((string) $this->request->getPost('author_name'));
                $designationId = trim((string) $this->request->getPost('designation_id'));
                $departmentId = filter_var($this->request->getPost('department_id'), FILTER_VALIDATE_INT);
                $subjectId = filter_var($this->request->getPost('subject_id'), FILTER_VALIDATE_INT);
                // Account Role is not required on the Teacher form. Keep the existing default role ID.
                $rollPost = $this->request->getPost('RollId');
                $rollId = ($rollPost === null || $rollPost === '')
                    ? 2
                    : filter_var($rollPost, FILTER_VALIDATE_INT);

                if ($name === '' || mb_strlen($name) > 255) {
                    $error = 'Teacher name is required and must not exceed 255 characters.';
                } elseif (! $departmentId || $db->table('mcq_tbl_departments')->where('department_id', $departmentId)->countAllResults() === 0) {
                    $error = 'Please select a valid department.';
                } elseif (! $subjectId || $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->where('department_id', $departmentId)->countAllResults() === 0) {
                    $error = 'Please select a subject belonging to the selected department.';
                } elseif ($rollId === false || $rollId === null || $rollId < 0) {
                    $error = 'Invalid role ID.';
                } elseif ($designationId !== '' && $db->table('mcq_tbl_designations')->where('designation_id', (int) $designationId)->countAllResults() === 0) {
                    $error = 'Please select a valid designation.';
                } else {
                    $duplicate = $db->table('mcq_tbl_author')->where('author_name', $name);
                    if ($id) $duplicate->where('author_id !=', $id);
                    if ($duplicate->countAllResults() > 0) {
                        $error = 'A teacher with this name already exists.';
                    } else {
                        $data = [
                            'author_name' => $name,
                            'designation_id' => $designationId === '' ? null : (int) $designationId,
                            'department_id' => $departmentId,
                            'subject_id' => $subjectId,
                            'RollId' => $rollId,
                        ];
                        if ($id) {
                            $exists = $db->table('mcq_tbl_author')->where('author_id', $id)->countAllResults() > 0;
                            if (! $exists) {
                                $error = 'Teacher record not found. Refresh and try again.';
                            } elseif ($db->table('mcq_tbl_author')->where('author_id', $id)->update($data)) {
                                return redirect()->to(site_url('master/teachers'))->with('success', 'Teacher record updated successfully.');
                            } else {
                                $error = 'Teacher record could not be updated. Check the application error log.';
                            }
                        } else {
                            // author_id is not AUTO_INCREMENT in the current database.
                            // Generate the next ID without changing any existing teacher IDs.
                            $nextIdRow = $db->query(
                                'SELECT COALESCE(MAX(author_id), 0) + 1 AS next_id FROM mcq_tbl_author'
                            )->getRowArray();
                            $nextId = isset($nextIdRow['next_id']) ? (int) $nextIdRow['next_id'] : 0;

                            if ($nextId < 1) {
                                $error = 'A new teacher ID could not be generated. Please check the database.';
                            } else {
                                $insertData = ['author_id' => $nextId] + $data;
                                if ($db->table('mcq_tbl_author')->insert($insertData)) {
                                    return redirect()->to(site_url('master/teachers'))->with('success', 'Teacher added successfully.');
                                }
                                $error = 'Teacher could not be added. Check the application error log.';
                            }
                        }
                    }
                }
            } else {
                $error = 'Invalid request.';
            }
        }

        $records = $db->table('mcq_tbl_author a')
            ->select('a.author_id, a.author_name, a.designation_id, a.department_id, a.subject_id, a.RollId, d.department_name, s.subject_name, g.designation_name')
            ->join('mcq_tbl_departments d', 'd.department_id = a.department_id', 'left')
            ->join('mcq_tbl_subject s', 's.subject_id = a.subject_id', 'left')
            ->join('mcq_tbl_designations g', 'g.designation_id = a.designation_id', 'left')
            ->orderBy('a.author_name', 'ASC')->get()->getResultArray();

        return view('teachers/index', [
            'records' => $records, 'subjects' => $subjects, 'departments' => $departments,
            'designations' => $designations, 'error' => $error,
            'success' => session()->getFlashdata('success'),
        ]);
    }
}
