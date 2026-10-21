<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;

class MasterData extends BaseController
{
    private function definitions(): array
    {
        return [
            'departments' => [
                'title' => 'Departments', 'table' => 'mcq_tbl_departments',
                'id' => 'department_id', 'name' => 'department_name',
                'nameLabel' => 'Department name', 'parent' => null,
                'columns' => ['department_id' => 'ID', 'department_name' => 'Department'],
            ],
            'subjects' => [
                'title' => 'Subjects', 'table' => 'mcq_tbl_subject',
                'id' => 'subject_id', 'name' => 'subject_name', 'nameLabel' => 'Subject name',
                'parent' => ['field' => 'department_id', 'table' => 'mcq_tbl_departments', 'id' => 'department_id', 'label' => 'department_name', 'title' => 'Department'],
                'columns' => ['subject_id' => 'ID', 'subject_name' => 'Subject', 'department_name' => 'Department'],
            ],
            'subdisciplines' => [
                'title' => 'Sub Disciplines', 'table' => 'mcq_tbl_subdiscipline',
                'id' => 'subdiscipline_id', 'name' => 'subdiscipline', 'nameLabel' => 'Sub Discipline name',
                'parent' => ['field' => 'subject_id', 'table' => 'mcq_tbl_subject', 'id' => 'subject_id', 'label' => 'subject_name', 'title' => 'Subject'],
                'columns' => ['subdiscipline_id' => 'ID', 'subdiscipline' => 'Sub Discipline', 'subject_name' => 'Subject'],
            ],
            'systems' => [
                'title' => 'Systems', 'table' => 'mcq_tbl_system',
                'id' => 'system_id', 'name' => 'system', 'nameLabel' => 'System name',
                'parent' => ['field' => 'subject_id', 'table' => 'mcq_tbl_subject', 'id' => 'subject_id', 'label' => 'subject_name', 'title' => 'Subject'],
                'columns' => ['system_id' => 'ID', 'system' => 'System', 'subject_name' => 'Subject', 'subdiscipline' => 'Sub Discipline'],
            ],
            'topics' => [
                'title' => 'Topics', 'table' => 'mcq_tbl_topic',
                'id' => 'topic_id', 'name' => 'topic', 'nameLabel' => 'Topic name',
                'parent' => ['field' => 'system_id', 'table' => 'mcq_tbl_system', 'id' => 'system_id', 'label' => 'system', 'title' => 'Main System'],
                'columns' => ['topic_id' => 'ID', 'topic' => 'Topic', 'system' => 'Main System', 'subject_name' => 'Subject', 'subdiscipline' => 'Sub Discipline'],
            ],
        ];
    }

    public function departments()    { return $this->handle('departments'); }
    public function subjects()       { return $this->handle('subjects'); }
    public function subdisciplines() { return $this->handle('subdisciplines'); }
    public function systems()        { return $this->handle('systems'); }
    public function topics()         { return $this->handle('topics'); }

    public function subdisciplinesBySubject(int $subjectId): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'You are not authorized to access this resource.']);
        }

        $db = db_connect();
        $subjectExists = $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->countAllResults() > 0;
        if (! $subjectExists) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'The selected subject was not found.']);
        }

        $items = $db->table('mcq_tbl_subdiscipline')
            ->select('subdiscipline_id AS id, subdiscipline AS name')
            ->where('subject_id', $subjectId)->orderBy('subdiscipline', 'ASC')->get()->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'subdisciplines' => $items]);
    }

    public function systemsBySubject(int $subjectId): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'You are not authorized to access this resource.']);
        }

        $db = db_connect();
        $subjectExists = $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->countAllResults() > 0;
        if (! $subjectExists) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'The selected subject was not found.']);
        }

        $disciplineId = filter_var($this->request->getGet('subdiscipline_id'), FILTER_VALIDATE_INT);
        $hasDisciplines = $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;

        $builder = $db->table('mcq_tbl_system')
            ->select('system_id AS id, system AS name')
            ->where('subject_id', $subjectId);

        if ($hasDisciplines) {
            if (! $disciplineId) {
                return $this->response->setJSON([
                    'status' => 'success', 'systems' => [], 'hasSubDisciplines' => true,
                    'message' => 'Select a Sub Discipline first.',
                ]);
            }
            $validDiscipline = $db->table('mcq_tbl_subdiscipline')
                ->where('subdiscipline_id', $disciplineId)->where('subject_id', $subjectId)->countAllResults() > 0;
            if (! $validDiscipline) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'The selected Sub Discipline does not belong to this Subject.']);
            }
            $builder->where('subdiscipline_id', $disciplineId);
        } elseif ($disciplineId) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'This Subject does not use Sub Disciplines.']);
        } else {
            $builder->where('subdiscipline_id', null);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'systems' => $builder->orderBy('system', 'ASC')->get()->getResultArray(),
            'hasSubDisciplines' => $hasDisciplines,
        ]);
    }

    private function isAdmin(): bool
    {
        return (bool) session()->get('isLoggedIn') && in_array((string) session()->get('role'), ['0', '1'], true);
    }

    private function handle(string $key)
    {
        if (! session()->get('isLoggedIn')) return redirect()->to(site_url('login'));
        if (! in_array((string) session()->get('role'), ['0', '1'], true)) {
            return redirect()->to(site_url('dashboard/member'))->with('error', 'Only administrators can access this page.');
        }

        $defs = $this->definitions();
        if (! isset($defs[$key])) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $cfg = $defs[$key];
        $db = db_connect();
        $error = null;
        $selectedSubjectId = '';
        $selectedSubdisciplineId = '';
        $editId = '';

        if (strtolower($this->request->getMethod()) === 'post') {
            $editId = trim((string) $this->request->getPost('record_id'));
            $name = trim((string) $this->request->getPost('name'));
            if ($name === '' || mb_strlen($name) > 255) {
                $error = 'The name field is required and must not exceed 255 characters.';
            } else {
                $insert = [$cfg['name'] => $name];
                $parent = $cfg['parent'];

                if ($key === 'subdisciplines') {
                    $subjectId = filter_var($this->request->getPost('parent_id'), FILTER_VALIDATE_INT);
                    if (! $subjectId || ! $this->exists($db, 'mcq_tbl_subject', 'subject_id', $subjectId)) {
                        $error = 'Please select a valid Subject.';
                    } else {
                        $insert['subject_id'] = $subjectId;
                    }
                } elseif ($key === 'systems' || $key === 'topics') {
                    $selectedSubjectId = (string) $this->request->getPost('subject_id');
                    $selectedSubdisciplineId = (string) $this->request->getPost('subdiscipline_id');
                    $subjectId = filter_var($selectedSubjectId, FILTER_VALIDATE_INT);
                    $disciplineId = filter_var($selectedSubdisciplineId, FILTER_VALIDATE_INT);

                    if (! $subjectId || ! $this->exists($db, 'mcq_tbl_subject', 'subject_id', $subjectId)) {
                        $error = 'Please select a valid Subject.';
                    } else {
                        $hasDisciplines = $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;
                        if ($hasDisciplines) {
                            $validDiscipline = $disciplineId && $db->table('mcq_tbl_subdiscipline')
                                ->where('subdiscipline_id', $disciplineId)->where('subject_id', $subjectId)->countAllResults() > 0;
                            if (! $validDiscipline) $error = 'Please select a valid Sub Discipline for this Subject.';
                        } elseif ($disciplineId) {
                            $error = 'The selected Subject does not use Sub Disciplines.';
                        }
                    }

                    if ($error === null && $key === 'systems') {
                        $insert['subject_id'] = $subjectId;
                        $insert['subdiscipline_id'] = $disciplineId ?: null;
                    } elseif ($error === null && $key === 'topics') {
                        $systemId = filter_var($this->request->getPost('parent_id'), FILTER_VALIDATE_INT);
                        $systemQuery = $db->table('mcq_tbl_system')->where('system_id', $systemId ?: 0)->where('subject_id', $subjectId);
                        if ($hasDisciplines) $systemQuery->where('subdiscipline_id', $disciplineId);
                        else $systemQuery->where('subdiscipline_id', null);
                        if (! $systemId || $systemQuery->countAllResults() === 0) {
                            $error = 'Please select a Main System that belongs to the selected Subject and Sub Discipline.';
                        } else {
                            $insert['system_id'] = $systemId;
                        }
                    }
                } elseif ($parent !== null) {
                    $parentId = filter_var($this->request->getPost('parent_id'), FILTER_VALIDATE_INT);
                    if (! $parentId || ! $this->exists($db, $parent['table'], $parent['id'], $parentId)) {
                        $error = 'Please select a valid ' . strtolower($parent['title']) . '.';
                    } else {
                        $insert[$parent['field']] = $parentId;
                    }
                }

                if ($error === null) {
                    if ($editId !== '') {
                        $validEditId = filter_var($editId, FILTER_VALIDATE_INT);
                        if (! $validEditId || ! $this->exists($db, $cfg['table'], $cfg['id'], (int) $validEditId)) {
                            $error = 'The selected record could not be found. Please select it again.';
                        } elseif ($db->table($cfg['table'])->where($cfg['id'], (int) $validEditId)->update($insert)) {
                            return redirect()->to(site_url('master/' . $key))->with('success', $cfg['title'] . ' record updated successfully.');
                        } else {
                            $error = 'The record could not be updated. Please check the database configuration and error log.';
                        }
                    } elseif ($db->table($cfg['table'])->insert($insert)) {
                        return redirect()->to(site_url('master/' . $key))->with('success', $cfg['title'] . ' record added successfully.');
                    } else {
                        $error = 'The record could not be saved. Please check the database configuration and error log.';
                    }
                }
            }
        }

        $builder = $db->table($cfg['table']);
        if ($key === 'subjects') {
            $builder->select('mcq_tbl_subject.subject_id, mcq_tbl_subject.subject_name, mcq_tbl_departments.department_name')
                ->join('mcq_tbl_departments', 'mcq_tbl_departments.department_id = mcq_tbl_subject.department_id');
        } elseif ($key === 'subdisciplines') {
            $builder->select('mcq_tbl_subdiscipline.subdiscipline_id, mcq_tbl_subdiscipline.subdiscipline, mcq_tbl_subject.subject_name')
                ->join('mcq_tbl_subject', 'mcq_tbl_subject.subject_id = mcq_tbl_subdiscipline.subject_id');
        } elseif ($key === 'systems') {
            $builder->select('mcq_tbl_system.system_id, mcq_tbl_system.system, mcq_tbl_subject.subject_name, mcq_tbl_subdiscipline.subdiscipline')
                ->join('mcq_tbl_subject', 'mcq_tbl_subject.subject_id = mcq_tbl_system.subject_id')
                ->join('mcq_tbl_subdiscipline', 'mcq_tbl_subdiscipline.subdiscipline_id = mcq_tbl_system.subdiscipline_id', 'left');
        } elseif ($key === 'topics') {
            $builder->select('mcq_tbl_topic.topic_id, mcq_tbl_topic.topic, mcq_tbl_system.system, mcq_tbl_subject.subject_name, mcq_tbl_subdiscipline.subdiscipline')
                ->join('mcq_tbl_system', 'mcq_tbl_system.system_id = mcq_tbl_topic.system_id')
                ->join('mcq_tbl_subject', 'mcq_tbl_subject.subject_id = mcq_tbl_system.subject_id')
                ->join('mcq_tbl_subdiscipline', 'mcq_tbl_subdiscipline.subdiscipline_id = mcq_tbl_system.subdiscipline_id', 'left');
        }
        $records = $builder->orderBy($cfg['id'], 'DESC')->get()->getResultArray();

        $parents = [];
        if ($cfg['parent'] !== null && ! in_array($key, ['systems', 'topics'], true)) {
            $p = $cfg['parent'];
            $parents = $db->table($p['table'])->select($p['id'] . ' AS id, ' . $p['label'] . ' AS label')
                ->orderBy($p['label'], 'ASC')->get()->getResultArray();
        }

        $subjects = [];
        if (in_array($key, ['systems', 'topics'], true)) {
            $subjects = $db->table('mcq_tbl_subject')->select('subject_id AS id, subject_name AS label')
                ->orderBy('subject_name', 'ASC')->get()->getResultArray();
        }

        return view('master_data/index', [
            'cfg' => $cfg, 'records' => $records, 'parents' => $parents, 'subjects' => $subjects,
            'selectedSubjectId' => $selectedSubjectId, 'selectedSubdisciplineId' => $selectedSubdisciplineId,
            'error' => $error, 'success' => session()->getFlashdata('success'), 'editId' => $editId,
        ]);
    }

    private function exists(BaseConnection $db, string $table, string $idColumn, int $id): bool
    {
        return $db->table($table)->where($idColumn, $id)->countAllResults() > 0;
    }
}
