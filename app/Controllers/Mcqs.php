<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class Mcqs extends BaseController
{
    private function roleId(): string
    {
        return (string) session()->get('role');
    }

    private function signedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function isFaculty(): bool
    {
        return $this->roleId() === '2';
    }

    private function isAdmin(): bool
    {
        // Role 2 is the faculty role in the current account data. Other roles are administrative.
        return $this->roleId() !== '2';
    }

    private function facultyAuthor(): ?array
    {
        if (! $this->signedIn() || ! $this->isFaculty()) return null;
        $userId = (int) session()->get('user_id');
        if ($userId < 1) return null;
        $author = db_connect()->table('mcq_tbl_author')
            ->where('author_id', $userId)->get()->getRowArray();
        if (! $author || (int) ($author['subject_id'] ?? 0) < 1) return null;
        return $author;
    }

    private function requireLogin()
    {
        if (! $this->signedIn()) return redirect()->to(site_url('login'));
        return null;
    }

    private function facultyOrAdminRedirect()
    {
        if ($r = $this->requireLogin()) return $r;
        if ($this->isFaculty() && ! $this->facultyAuthor()) {
            return redirect()->to(site_url('dashboard/member'))
                ->with('error', 'Your faculty account has no valid Author and Subject assignment. Please contact the administrator.');
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->requireLogin()) return $r;
        if ($this->isFaculty()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $db = db_connect();
        $search = trim((string) $this->request->getGet('q'));
        $builder = $db->table('mcq_tbl_mcqs m')
            ->select('m.MCQID, m.mcqQuestion, m.mcqOption1, m.mcqOption2, m.mcqOption3, m.mcqOption4, m.mcqOption5, m.mcqKey, s.subject_name, sy.system, t.topic')
            ->join('mcq_tbl_subject s', 's.subject_id = m.SubjectID', 'left')
            ->join('mcq_tbl_system sy', 'sy.system_id = m.SystemID', 'left')
            ->join('mcq_tbl_topic t', 't.topic_id = m.TopicID', 'left');
        if ($search !== '') {
            $builder->groupStart()->like('m.mcqQuestion', $search)->orLike('m.MCQID', $search)
                ->orLike('s.subject_name', $search)->orLike('sy.system', $search)->orLike('t.topic', $search)->groupEnd();
        }
        return view('mcqs/index', ['title' => 'Approved MCQ Bank', 'search' => $search,
            'mcqs' => $builder->orderBy('m.MCQID', 'DESC')->get(20)->getResultArray(), 'pager' => null]);
    }

    public function new()
    {
        if ($r = $this->facultyOrAdminRedirect()) return $r;
        if (! $this->isFaculty()) return redirect()->to(site_url('master/mcqs/submissions'));
        $db = db_connect(); $author = $this->facultyAuthor();
        $subject = $db->table('mcq_tbl_subject')->where('subject_id', (int) $author['subject_id'])->get()->getRowArray();
        if (! $subject) return redirect()->to(site_url('dashboard/member'))->with('error', 'Your assigned Subject could not be found.');
        return view('mcqs/form', [
            'title' => 'Create MCQ Submission', 'author' => $author,
            'subjects' => [$subject], 'programs' => $db->table('mcq_tbl_programs')->where('is_Active', 1)->orderBy('progName')->get()->getResultArray(),
            'selectedSubjectId' => (string) $subject['subject_id'], 'selectedSubdisciplineId' => old('subdiscipline_id', ''),
            'selectedSystemId' => old('system_id', ''), 'selectedTopicId' => old('topic_id', ''),
            'action' => site_url('master/mcqs/save'), 'today' => date('Y-m-d'),
        ]);
    }

    public function save()
    {
        if ($r = $this->facultyOrAdminRedirect()) return $r;
        if (! $this->isFaculty()) return redirect()->to(site_url('master/mcqs/submissions'));
        $author = $this->facultyAuthor();
        if (! $author) return redirect()->to(site_url('dashboard/member'))->with('error', 'Your faculty account has no valid Author and Subject assignment.');

        $intent = (string) $this->request->getPost('intent');
        if (! in_array($intent, ['draft', 'submit'], true)) $intent = 'draft';
        $subjectId = (int) $this->request->getPost('subject_id');
        // Enforce assigned Subject on the server. Never trust the browser's dropdown.
        if ($subjectId !== (int) $author['subject_id']) {
            return redirect()->back()->withInput()->with('errors', ['subject_id' => 'You may enter MCQs only for your assigned Subject.']);
        }
        $systemId = (int) $this->request->getPost('system_id');
        $topicId = (int) $this->request->getPost('topic_id');
        $disciplineId = (int) $this->request->getPost('subdiscipline_id');
        $programId = (int) $this->request->getPost('progId');
        $competency = (int) $this->request->getPost('competency');
        $difficulty = (string) $this->request->getPost('difficulty');
        $stem = trim((string) $this->request->getPost('stem'));
        $leadIn = trim((string) $this->request->getPost('leadin'));
        $options = [];
        for ($i = 1; $i <= 5; $i++) $options[$i] = trim((string) $this->request->getPost('option' . $i));
        $answer = strtoupper(trim((string) $this->request->getPost('mkey')));

        $errors = [];
        if ($systemId < 1 || $topicId < 1) $errors['hierarchy'] = 'Select a valid System and Topic.';
        if ($programId < 1 || db_connect()->table('mcq_tbl_programs')->where('progId', $programId)->where('is_Active', 1)->countAllResults() < 1) $errors['progId'] = 'Select a valid Program.';
        if ($competency < 1 || $competency > 4) $errors['competency'] = 'Select Competency 1, 2, 3, or 4.';
        if (! in_array($difficulty, ['Difficult', 'Moderate', 'Easy'], true)) $errors['difficulty'] = 'Select a valid Difficulty Level.';
        if ($intent === 'submit') {
            if ($stem === '') $errors['stem'] = 'Stem is required to submit.';
            if ($leadIn === '') $errors['leadin'] = 'Lead-in is required to submit.';
            foreach ($options as $i => $value) if ($value === '') $errors['option' . $i] = 'All five answer options are required to submit.';
            if (! in_array($answer, ['A','B','C','D','E'], true)) $errors['mkey'] = 'Select the correct answer.';
        }
        if ($errors) return redirect()->back()->withInput()->with('errors', $errors);

        $db = db_connect();
        $subject = $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->get()->getRowArray();
        $hasDisciplines = $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;
        if ($hasDisciplines) {
            if (! $disciplineId || $db->table('mcq_tbl_subdiscipline')->where('subdiscipline_id', $disciplineId)->where('subject_id', $subjectId)->countAllResults() < 1) return redirect()->back()->withInput()->with('errors', ['subdiscipline_id' => 'Select a valid Sub Discipline for your Subject.']);
        } else { $disciplineId = 0; }
        $systemQuery = $db->table('mcq_tbl_system')->where('system_id', $systemId)->where('subject_id', $subjectId);
        if ($hasDisciplines) $systemQuery->where('subdiscipline_id', $disciplineId); else $systemQuery->where('subdiscipline_id', null);
        if ($systemQuery->countAllResults() < 1 || $db->table('mcq_tbl_topic')->where('topic_id', $topicId)->where('system_id', $systemId)->countAllResults() < 1) return redirect()->back()->withInput()->with('errors', ['hierarchy' => 'The selected Subject, Sub Discipline, System, and Topic do not match.']);

        $payload = [
            'author_id' => (int) $author['author_id'], 'subject_id' => $subjectId, 'system_id' => $systemId,
            'topic_id' => $topicId, 'subtopic_id' => 0,
            'book_title' => trim((string) $this->request->getPost('book_title')),
            'name_of_bookauthor' => trim((string) $this->request->getPost('name_of_bookauthor')),
            'edition' => trim((string) $this->request->getPost('edition')),
            'page_no' => trim((string) $this->request->getPost('page_no')),
            'learning_objective' => trim((string) $this->request->getPost('learning_objective')),
            'competency' => $competency, 'stem' => $stem, 'leadin' => $leadIn,
            'option1' => $options[1], 'option2' => $options[2], 'option3' => $options[3], 'option4' => $options[4], 'option5' => $options[5],
            'mkey' => $answer === '' ? null : $answer, 'mdate' => date('Y-m-d H:i:s'),
            'status' => 0, 'difficulty' => $difficulty, 'progId' => $programId,
            'workflow_state' => $intent === 'submit' ? 'submitted' : 'draft',
        ];
        if (! $db->table('mcq_tbl_mcqstempsaved')->insert($payload)) {
            log_message('error', 'MCQ submission insert failed: ' . json_encode($db->error()));
            return redirect()->back()->withInput()->with('errors', ['database' => 'Unable to save the question. Check writable/logs for the database error.']);
        }
        $message = $intent === 'submit' ? 'Question submitted to the administrator. It is no longer listed among your drafts.' : 'Draft saved. You can return to My Questions to complete it.';
        return redirect()->to(site_url('master/mcqs/my-questions'))->with('success', $message);
    }

    public function myQuestions()
    {
        if ($r = $this->facultyOrAdminRedirect()) return $r;
        if (! $this->isFaculty()) return redirect()->to(site_url('master/mcqs/submissions'));
        $author = $this->facultyAuthor();
        $rows = db_connect()->table('mcq_tbl_mcqstempsaved q')
            ->select('q.mcq_id, q.stem, q.leadin, q.mdate, q.workflow_state, q.difficulty, s.subject_name, sy.system, t.topic')
            ->join('mcq_tbl_subject s', 's.subject_id=q.subject_id', 'left')->join('mcq_tbl_system sy', 'sy.system_id=q.system_id', 'left')->join('mcq_tbl_topic t', 't.topic_id=q.topic_id', 'left')
            ->where('q.author_id', (int) $author['author_id'])->where('q.workflow_state', 'draft')->orderBy('q.mcq_id', 'DESC')->get()->getResultArray();
        return view('mcqs/my_questions', ['title' => 'My Draft Questions', 'questions' => $rows]);
    }

    public function submit(int $id)
    {
        if ($r = $this->facultyOrAdminRedirect()) return $r;
        if (! $this->isFaculty()) return redirect()->to(site_url('master/mcqs/submissions'));
        $author = $this->facultyAuthor();
        $db = db_connect();
        $q = $db->table('mcq_tbl_mcqstempsaved')->where('mcq_id', $id)->where('author_id', (int) $author['author_id'])->where('workflow_state', 'draft')->get()->getRowArray();
        if (! $q) return redirect()->to(site_url('master/mcqs/my-questions'))->with('error', 'Draft not found or it has already been submitted.');
        $missing = [];
        foreach (['stem','leadin','option1','option2','option3','option4','option5','mkey'] as $field) if (trim((string) ($q[$field] ?? '')) === '') $missing[] = $field;
        if ($missing) return redirect()->to(site_url('master/mcqs/my-questions'))->with('error', 'Complete Stem, Lead-in, all five options, and correct answer before submitting.');
        $db->table('mcq_tbl_mcqstempsaved')->where('mcq_id', $id)->update(['workflow_state' => 'submitted']);
        return redirect()->to(site_url('master/mcqs/my-questions'))->with('success', 'Question submitted to the administrator.');
    }

    /** Build a filtered list of submitted MCQs. Department is taken from the faculty record. */
    private function submittedQuery(array $filters = [])
    {
        $db = db_connect();
        $builder = $db->table('mcq_tbl_mcqstempsaved q')
            ->select('q.mcq_id, q.stem, q.leadin, q.mdate, q.status, q.difficulty, q.competency, q.progId, q.author_id, a.author_name, a.department_id AS faculty_department_id, s.subject_name, sy.system, t.topic, p.progName, dep.department_name')
            ->join('mcq_tbl_author a', 'a.author_id=q.author_id', 'left')
            ->join('mcq_tbl_subject s', 's.subject_id=q.subject_id', 'left')
            ->join('mcq_tbl_system sy', 'sy.system_id=q.system_id', 'left')
            ->join('mcq_tbl_topic t', 't.topic_id=q.topic_id', 'left')
            ->join('mcq_tbl_programs p', 'p.progId=q.progId', 'left')
            ->join('mcq_tbl_departments dep', 'dep.department_id=a.department_id', 'left')
            ->where('q.status', 1);
        if (!empty($filters['department_id'])) $builder->where('a.department_id', (int) $filters['department_id']);
        if (!empty($filters['author_id'])) $builder->where('q.author_id', (int) $filters['author_id']);
        if (!empty($filters['q'])) {
            $needle = trim((string) $filters['q']);
            $builder->groupStart()->like('q.stem', $needle)->orLike('q.leadin', $needle)->orLike('q.mcq_id', $needle)->orLike('a.author_name', $needle)->groupEnd();
        }
        return $builder;
    }

    public function submissions()
    {
        if ($r = $this->requireLogin()) return $r;
        if (! $this->isAdmin()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $db = db_connect();
        $filters = [
            'department_id' => (int) $this->request->getGet('department_id'),
            'author_id' => (int) $this->request->getGet('author_id'),
            'q' => trim((string) $this->request->getGet('q')),
        ];
        $departments = $db->table('mcq_tbl_departments')->orderBy('department_name', 'ASC')->get()->getResultArray();
        $facultyBuilder = $db->table('mcq_tbl_author a')->select('a.author_id, a.author_name, a.department_id')
            ->join('mcq_tbl_mcqstempsaved q', 'q.author_id=a.author_id', 'inner')->where('q.status', 1)->groupBy('a.author_id, a.author_name, a.department_id');
        if ($filters['department_id'] > 0) $facultyBuilder->where('a.department_id', $filters['department_id']);
        $faculty = $facultyBuilder->orderBy('a.author_name', 'ASC')->get()->getResultArray();
        $questions = $this->submittedQuery($filters)->orderBy('q.mcq_id', 'DESC')->get()->getResultArray();
        return view('mcqs/submissions', [
            'title' => 'Faculty Submissions', 'questions' => $questions, 'departments' => $departments,
            'faculty' => $faculty, 'filters' => $filters,
        ]);
    }

    /** Download only the currently filtered submitted questions; downloading never changes their status. */
    public function downloadSubmissions()
    {
        if ($r = $this->requireLogin()) return $r;
        if (! $this->isAdmin()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $filters = [
            'department_id' => (int) $this->request->getGet('department_id'),
            'author_id' => (int) $this->request->getGet('author_id'),
            'q' => trim((string) $this->request->getGet('q')),
        ];
        $columns = ['mcq_id','author_id','subject_id','system_id','topic_id','subtopic_id','book_title','name_of_bookauthor','edition','page_no','learning_objective','competency','stem','leadin','option1','option2','option3','option4','option5','mkey','mdate','status','difficulty','progId','primary_language','secondary_language','scenario','stem_secondary','scenario_secondary','leadin_secondary','option1_secondary','option2_secondary','option3_secondary','option4_secondary','option5_secondary'];
        $builder = db_connect()->table('mcq_tbl_mcqstempsaved q')->select(implode(',', array_map(static fn($c) => 'q.'.$c, $columns)))
            ->join('mcq_tbl_author a', 'a.author_id=q.author_id', 'left')->where('q.status', 1);
        if ($filters['department_id'] > 0) $builder->where('a.department_id', $filters['department_id']);
        if ($filters['author_id'] > 0) $builder->where('q.author_id', $filters['author_id']);
        if ($filters['q'] !== '') $builder->groupStart()->like('q.stem', $filters['q'])->orLike('q.leadin', $filters['q'])->orLike('q.mcq_id', $filters['q'])->orLike('a.author_name', $filters['q'])->groupEnd();
        $rows = $builder->orderBy('q.mcq_id', 'ASC')->get()->getResultArray();
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, $columns);
        foreach ($rows as $row) { $line=[]; foreach ($columns as $column) $line[]=$row[$column] ?? ''; fputcsv($stream, $line); }
        rewind($stream); $csv=stream_get_contents($stream); fclose($stream);
        $suffix = $filters['author_id'] ? '_faculty'.$filters['author_id'] : ($filters['department_id'] ? '_department'.$filters['department_id'] : '_all');
        $filename = 'faculty_submissions'.$suffix.'_'.date('Ymd_His').'.csv';
        return $this->response->download($filename, $csv)->setFileName($filename)->setContentType('text/csv; charset=UTF-8');
    }

    public function review(int $id)
    {
        if ($r = $this->requireLogin()) return $r;
        if (! $this->isAdmin()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $state = (string) $this->request->getPost('workflow_state');
        if (! in_array($state, ['approved','rejected'], true)) return redirect()->back()->with('error', 'Invalid review action.');
        $db = db_connect();
        $q = $db->table('mcq_tbl_mcqstempsaved')->where('mcq_id', $id)->where('workflow_state', 'submitted')->get()->getRowArray();
        if (! $q) return redirect()->back()->with('error', 'Only submitted questions can be reviewed.');
        $db->table('mcq_tbl_mcqstempsaved')->where('mcq_id', $id)->update(['workflow_state' => $state]);
        return redirect()->to(site_url('master/mcqs/submissions'))->with('success', 'Submission marked ' . $state . '. Export/transfer remains a separate step.');
    }

    // Retain editing for records already present in the approved MCQ bank.
    public function edit(int $id)
    {
        if ($r = $this->requireLogin()) return $r;
        if (! $this->isAdmin()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $db = db_connect();
        $mcq = $db->table('mcq_tbl_mcqs')->where('MCQID', $id)->get()->getRowArray();
        if (! $mcq) throw PageNotFoundException::forPageNotFound('MCQ not found.');
        $systemRow = $db->table('mcq_tbl_system')->where('system_id', $mcq['SystemID'])->get()->getRowArray();
        return view('mcqs/legacy_edit_notice', ['title' => 'Edit Approved MCQ', 'mcq' => $mcq, 'systemRow' => $systemRow]);
    }

    public function update(int $id)
    {
        if ($r = $this->requireLogin()) return $r;
        if (! $this->isAdmin()) return redirect()->to(site_url('master/mcqs/my-questions'));
        $db = db_connect();
        $existing = $db->table('mcq_tbl_mcqs')->where('MCQID', $id)->get()->getRowArray();
        if (! $existing) throw PageNotFoundException::forPageNotFound('MCQ not found.');
        $rules = [
            'mcqQuestion' => 'required|max_length[255]',
            'mcqOption1' => 'required|max_length[255]', 'mcqOption2' => 'required|max_length[255]',
            'mcqOption3' => 'required|max_length[255]', 'mcqOption4' => 'required|max_length[255]',
            'mcqOption5' => 'required|max_length[255]', 'mcqKey' => 'required|in_list[A,B,C,D,E]',
        ];
        if (! $this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $payload = [
            'mcqQuestion' => trim((string) $this->request->getPost('mcqQuestion')),
            'mcqOption1' => trim((string) $this->request->getPost('mcqOption1')),
            'mcqOption2' => trim((string) $this->request->getPost('mcqOption2')),
            'mcqOption3' => trim((string) $this->request->getPost('mcqOption3')),
            'mcqOption4' => trim((string) $this->request->getPost('mcqOption4')),
            'mcqOption5' => trim((string) $this->request->getPost('mcqOption5')),
            'mcqKey' => strtoupper((string) $this->request->getPost('mcqKey')),
        ];
        if (! $db->table('mcq_tbl_mcqs')->where('MCQID', $id)->update($payload)) return redirect()->back()->withInput()->with('errors', ['database' => 'The approved MCQ could not be updated.']);
        return redirect()->to(site_url('master/mcqs'))->with('success', 'Approved MCQ updated successfully.');
    }

    public function systemsBySubject(int $subjectId): ResponseInterface
    {
        if (! $this->signedIn()) return $this->response->setStatusCode(403)->setJSON(['message' => 'Not authorized.']);
        if ($this->isFaculty()) {
            $author = $this->facultyAuthor();
            if (! $author || (int) $author['subject_id'] !== $subjectId) return $this->response->setStatusCode(403)->setJSON(['message' => 'You may only access your assigned Subject.']);
        }
        $db = db_connect(); $disciplineId = (int) $this->request->getGet('subdiscipline_id');
        $hasDisciplines = $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;
        $builder = $db->table('mcq_tbl_system')->select('system_id AS id, system AS name')->where('subject_id', $subjectId);
        if ($hasDisciplines) {
            if (! $disciplineId) return $this->response->setJSON(['systems' => [], 'hasSubDisciplines' => true]);
            if ($db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->where('subdiscipline_id', $disciplineId)->countAllResults() === 0) return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid Sub Discipline.']);
            $builder->where('subdiscipline_id', $disciplineId);
        } else $builder->where('subdiscipline_id', null);
        return $this->response->setJSON(['systems' => $builder->orderBy('system')->get()->getResultArray(), 'hasSubDisciplines' => $hasDisciplines]);
    }

    public function topicsBySystem(int $systemId): ResponseInterface
    {
        if (! $this->signedIn()) return $this->response->setStatusCode(403)->setJSON(['message' => 'Not authorized.']);
        $items = db_connect()->table('mcq_tbl_topic')->select('topic_id AS id, topic AS name')->where('system_id', $systemId)->orderBy('topic')->get()->getResultArray();
        return $this->response->setJSON(['topics' => $items]);
    }
}
