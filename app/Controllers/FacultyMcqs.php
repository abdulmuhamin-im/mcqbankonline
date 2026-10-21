<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class FacultyMcqs extends BaseController
{
    private function facultyContext()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (! in_array((string) session()->get('role'), ['2', '3'], true)) {
            return redirect()->to(site_url('dashboard/admin'));
        }

        $authorId = (int) (session()->get('user_id') ?? 0);
        if ($authorId < 1) {
            return redirect()->to(site_url('dashboard/member'))
                ->with('error', 'Your login session does not contain a valid user ID.');
        }

        $db = db_connect();
        $author = $db->table('mcq_tbl_author')
            ->where('author_id', $authorId)
            ->get()->getRowArray();

        if (! $author) {
            return redirect()->to(site_url('dashboard/member'))
                ->with('error', 'Your account is not linked to a faculty author record. Please contact the administrator.');
        }

        return ['author' => $author, 'authorId' => $authorId, 'db' => $db];
    }

    /** Allow only simple formatting tags from the MCQ rich-text editor; strip attributes/scripts. */
    private function sanitizeRichText(string $html): string
    {
        $html = strip_tags($html, '<b><strong><i><em><u><sup><sub><br><p><div>');
        $html = preg_replace_callback('/<\/?([a-zA-Z0-9]+)\b[^>]*>/', static function (array $m): string {
            $tag = strtolower($m[1]);
            $allowed = ['b','strong','i','em','u','sup','sub','br','p','div'];
            if (!in_array($tag, $allowed, true)) return '';
            $closing = str_starts_with($m[0], '</');
            if ($tag === 'br') return '<br>';
            return $closing ? '</' . $tag . '>' : '<' . $tag . '>';
        }, $html) ?? '';
        // Prevent an empty editor containing only line breaks from satisfying required lead-in.
        return trim($html);
    }

    public function index()
    {
        $context = $this->facultyContext();
        if ($context instanceof \CodeIgniter\HTTP\RedirectResponse) return $context;

        $db = $context['db'];
        $author = $context['author'];
        $authorId = $context['authorId'];

        $subject = $db->table('mcq_tbl_subject')
            ->where('subject_id', (int) $author['subject_id'])
            ->get()->getRowArray();

        $systems = $db->table('mcq_tbl_system')
            ->where('subject_id', (int) $author['subject_id'])
            ->orderBy('system')
            ->get()->getResultArray();

        $draftQuery = $db->table('mcq_tbl_mcqstempsaved tmp')
            ->select('tmp.*')
            ->where('tmp.author_id', $authorId);

        // Status 0 is treated as draft and status 1 as submitted in this workflow.
        // Existing records are not changed by opening this page.
        $drafts = $draftQuery->where('tmp.status', 0)
            ->orderBy('tmp.mcq_id', 'DESC')->get()->getResultArray();

        // Show only programs assigned to this faculty member for this subject.
        $programs = $this->assignedPrograms($db, $authorId, (int) $author['subject_id'], $subject ?? []);
        $hasProgramAssignments = $db->tableExists('mcq_tbl_faculty_subject_program');

        return view('faculty/mcqs', [
            'pageTitle' => 'MCQ Entry & Drafts',
            'author' => $author,
            'subject' => $subject,
            'systems' => $systems,
            'drafts' => $drafts,
            'programs' => $programs,
            'hasPrograms' => $db->tableExists('mcq_tbl_programs'),
            'hasProgramAssignments' => $hasProgramAssignments,
            'errors' => session()->getFlashdata('errors') ?? [],
            'success' => session()->getFlashdata('success'),
            'old' => session()->getFlashdata('_ci_old_input') ?? [],
        ]);
    }

    public function save()
    {
        $context = $this->facultyContext();
        if ($context instanceof \CodeIgniter\HTTP\RedirectResponse) return $context;

        $db = $context['db'];
        $author = $context['author'];
        $authorId = $context['authorId'];
        $subjectId = (int) $author['subject_id'];

        $action = (string) $this->request->getPost('action');
        if (! in_array($action, ['draft', 'submit'], true)) {
            return redirect()->back()->withInput()->with('errors', ['action' => 'Choose Save Draft or Submit for Review.']);
        }

        $rules = [
            'primary_language' => 'required|in_list[English,Urdu,Arabic]',
            'secondary_language' => 'permit_empty|in_list[English,Urdu,Arabic]',
            'stem' => 'permit_empty',
            'leadin' => 'required',
            'option1' => 'required|max_length[255]',
            'option2' => 'required|max_length[255]',
            'option3' => 'required|max_length[255]',
            'option4' => 'required|max_length[255]',
            'option5' => 'required|max_length[255]',
            'mkey' => 'required|in_list[A,B,C,D,E]',
            'system_id' => 'required|is_natural_no_zero',
            'topic_id' => 'required|is_natural_no_zero',
            'competency' => 'required|in_list[1,2,3,4]',
            'difficulty' => 'required|in_list[Easy,Moderate,Difficult]',
        ];

        // Program is mandatory for both drafts and submissions.
        $rules['program_id'] = 'required|is_natural_no_zero';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $safeLeadin = $this->sanitizeRichText((string) $this->request->getPost('leadin'));
        if (trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $safeLeadin)))) === '') {
            return redirect()->back()->withInput()->with('errors', ['leadin' => 'Lead-in / Question is required.']);
        }

        $image = $this->request->getFile('mcq_image');
        if ($image && $image->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $image->isValid()) {
                return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'The selected image could not be uploaded.']);
            }
            if ($image->getSizeByUnit('mb') > 6) {
                return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'Image size must not exceed 6 MB.']);
            }
            if (! in_array(strtolower($image->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)
                || ! in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
                return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'Upload a JPG, PNG, or WebP image.']);
            }
        }

        $primaryLanguage = (string) $this->request->getPost('primary_language');
        $secondaryLanguage = trim((string) $this->request->getPost('secondary_language'));
        if ($secondaryLanguage !== '' && $secondaryLanguage === $primaryLanguage) {
            return redirect()->back()->withInput()->with('errors', [
                'secondary_language' => 'Secondary Language must be different from Primary Language.'
            ]);
        }

        $systemId = (int) $this->request->getPost('system_id');
        $topicId = (int) $this->request->getPost('topic_id');
        $subdisciplineId = (int) $this->request->getPost('subdiscipline_id');
        $hasSubdisciplines = $db->tableExists('mcq_tbl_subdiscipline')
            && $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;

        $systemQuery = $db->table('mcq_tbl_system')
            ->where('system_id', $systemId)
            ->where('subject_id', $subjectId);

        $systemFields = $db->getFieldNames('mcq_tbl_system');
        if (in_array('subdiscipline_id', $systemFields, true)) {
            if ($hasSubdisciplines) {
                $validDisc = $db->table('mcq_tbl_subdiscipline')
                    ->where('subdiscipline_id', $subdisciplineId)
                    ->where('subject_id', $subjectId)->countAllResults() > 0;
                if (! $validDisc) {
                    return redirect()->back()->withInput()->with('errors', ['subdiscipline_id' => 'Select a valid Sub Discipline for your assigned subject.']);
                }
                $systemQuery->where('subdiscipline_id', $subdisciplineId);
            } else {
                $systemQuery->where('subdiscipline_id', null);
            }
        }

        if ($systemQuery->countAllResults() === 0) {
            return redirect()->back()->withInput()->with('errors', ['system_id' => 'The selected System is not part of your assigned Subject/Sub Discipline.']);
        }

        if ($db->table('mcq_tbl_topic')->where('topic_id', $topicId)->where('system_id', $systemId)->countAllResults() === 0) {
            return redirect()->back()->withInput()->with('errors', ['topic_id' => 'The selected Topic does not belong to the selected System.']);
        }

        $programId = (int) $this->request->getPost('program_id');
        $subjectRow = $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->get()->getRowArray();
        $assignedPrograms = $this->assignedPrograms($db, $authorId, $subjectId, $subjectRow ?? []);
        $programAllowed = false;
        foreach ($assignedPrograms as $assignedProgram) {
            if ((int) ($assignedProgram['progId'] ?? 0) === $programId) {
                $programAllowed = true;
                break;
            }
        }
        if (! $programAllowed) {
            return redirect()->back()->withInput()->with('errors', [
                'program_id' => 'Select a Program assigned to your faculty account for this Subject. Contact the administrator if your Program is missing.'
            ]);
        }

        $table = 'mcq_tbl_mcqstempsaved';
        $fields = $db->getFieldNames($table);
        $editingId = (int) $this->request->getPost('editing_id');
        $isUpdating = $editingId > 0;

        // Only update a draft owned by the logged-in author. Never insert a second row
        // when the form was opened through the Edit link.
        if ($isUpdating) {
            $existingDraft = $db->table($table)
                ->where('mcq_id', $editingId)
                ->where('author_id', $authorId)
                ->where('status', 0)
                ->get()->getRowArray();
            if (! $existingDraft) {
                return redirect()->to(site_url('master/faculty/mcqs'))
                    ->with('error', 'This draft was not found, does not belong to your account, or has already been submitted.');
            }
        }

        $existingImagePath = $isUpdating ? (string) ($existingDraft['image_path'] ?? '') : '';
        $newImagePath = ((string) $this->request->getPost('remove_mcq_image') === '1') ? '' : $existingImagePath;
        if ($image && $image->isValid() && ! $image->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/mcq_images';
            if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0750, true) && ! is_dir($uploadDir)) {
                return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'Could not create the image storage directory.']);
            }
            $newName = $image->getRandomName();
            if (! $image->move($uploadDir, $newName)) {
                return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'Could not store the selected image.']);
            }
            $newImagePath = 'uploads/mcq_images/' . $newName;
        }

        $maxId = (int) ($db->table($table)->selectMax('mcq_id', 'max_id')->get()->getRowArray()['max_id'] ?? 0);
        $nextId = $maxId + 1;
        $status = $action === 'draft' ? 0 : 1;

        $payload = [
            'author_id' => $authorId,
            'subject_id' => $subjectId,
            'system_id' => $systemId,
            'topic_id' => $topicId,
            'subtopic_id' => 0,
            'book_title' => trim((string) $this->request->getPost('book_title')),
            'name_of_bookauthor' => trim((string) $this->request->getPost('name_of_bookauthor')),
            'edition' => trim((string) $this->request->getPost('edition')),
            'page_no' => trim((string) $this->request->getPost('page_no')),
            'learning_objective' => trim((string) $this->request->getPost('learning_objective')),
            'competency' => (int) $this->request->getPost('competency'),
            'primary_language' => $primaryLanguage,
            'secondary_language' => $secondaryLanguage !== '' ? $secondaryLanguage : null,
            'stem' => $this->sanitizeRichText((string) $this->request->getPost('stem')),
            'scenario' => null,
            'leadin' => $safeLeadin,
            'stem_secondary' => $this->sanitizeRichText((string) $this->request->getPost('stem_secondary')),
            'scenario_secondary' => null,
            'leadin_secondary' => $this->sanitizeRichText((string) $this->request->getPost('leadin_secondary')),
            'option1_secondary' => trim((string) $this->request->getPost('option1_secondary')),
            'option2_secondary' => trim((string) $this->request->getPost('option2_secondary')),
            'option3_secondary' => trim((string) $this->request->getPost('option3_secondary')),
            'option4_secondary' => trim((string) $this->request->getPost('option4_secondary')),
            'option5_secondary' => trim((string) $this->request->getPost('option5_secondary')),
            'option1' => trim((string) $this->request->getPost('option1')),
            'option2' => trim((string) $this->request->getPost('option2')),
            'option3' => trim((string) $this->request->getPost('option3')),
            'option4' => trim((string) $this->request->getPost('option4')),
            'option5' => trim((string) $this->request->getPost('option5')),
            'mkey' => strtoupper((string) $this->request->getPost('mkey')),
            'mdate' => date('Y-m-d H:i:s'),
            'status' => $status,
        ];

        // Multilingual columns are optional for backward compatibility with older schemas.
        if ($secondaryLanguage === '') {
            foreach (['stem_secondary', 'scenario_secondary', 'leadin_secondary',
                      'option1_secondary', 'option2_secondary', 'option3_secondary',
                      'option4_secondary', 'option5_secondary'] as $secondaryField) {
                if (array_key_exists($secondaryField, $payload)) {
                    $payload[$secondaryField] = null;
                }
            }
        }

        // Include optional columns only when they exist in this installation.
        if (in_array('difficulty', $fields, true)) {
            $payload['difficulty'] = (string) $this->request->getPost('difficulty');
        }
        if (in_array('progId', $fields, true) && $programId > 0) {
            $payload['progId'] = $programId;
        }
        if (in_array('image_path', $fields, true)) {
            $payload['image_path'] = $newImagePath !== '' ? $newImagePath : null;
        } elseif ($image && $image->isValid()) {
            log_message('error', 'MCQ image selected but mcq_tbl_mcqstempsaved.image_path column is missing. Run the supplied SQL migration.');
            return redirect()->back()->withInput()->with('errors', ['mcq_image' => 'Image storage is not configured yet. Run the supplied database migration, then try again.']);
        }

        // Preserve compatibility with old NOT NULL columns and current schema.
        $payload = array_intersect_key($payload, array_flip($fields));
        $db->transStart();
        if ($isUpdating) {
            $saved = $db->table($table)
                ->where('mcq_id', $editingId)
                ->where('author_id', $authorId)
                ->where('status', 0)
                ->update($payload);
        } else {
            // This installation uses manually assigned mcq_id values; preserve that
            // behavior for new records only. Existing drafts are updated in place.
            $payload['mcq_id'] = $nextId;
            $payload = array_intersect_key($payload, array_flip($fields));
            $saved = $db->table($table)->insert($payload);
        }
        $db->transComplete();

        if (! $saved || $db->transStatus() === false) {
            log_message('error', 'Faculty MCQ save failed: ' . json_encode($db->error()));
            return redirect()->back()->withInput()->with('errors', ['database' => 'The question could not be saved. Please check the application log and database schema.']);
        }

        if ($existingImagePath !== '' && $existingImagePath !== $newImagePath) {
            $oldFile = FCPATH . str_replace(['..', '\\'], ['', '/'], $existingImagePath);
            if (is_file($oldFile)) @unlink($oldFile);
        }

        $message = $action === 'draft'
            ? 'Draft saved. You can continue editing it from My Drafts.'
            : 'MCQ submitted for administrative review.';
        return redirect()->to(site_url('master/faculty/mcqs'))->with('success', $message);
    }

    public function edit(int $id)
    {
        $context = $this->facultyContext();
        if ($context instanceof \CodeIgniter\HTTP\RedirectResponse) return $context;

        $db = $context['db'];
        $row = $db->table('mcq_tbl_mcqstempsaved')
            ->where('mcq_id', $id)
            ->where('author_id', $context['authorId'])
            ->where('status', 0)
            ->get()->getRowArray();

        if (! $row) {
            return redirect()->to(site_url('master/faculty/mcqs'))->with('error', 'Draft not found or it has already been submitted.');
        }
        // The form uses program_id, while the database stores it as progId.
        $row['program_id'] = $row['progId'] ?? $row['program_id'] ?? '';

        return view('faculty/mcqs', [
            'pageTitle' => 'Edit MCQ Draft',
            'author' => $context['author'],
            'subject' => $db->table('mcq_tbl_subject')->where('subject_id', (int) $context['author']['subject_id'])->get()->getRowArray(),
            'systems' => $db->table('mcq_tbl_system')->where('subject_id', (int) $context['author']['subject_id'])->orderBy('system')->get()->getResultArray(),
            'drafts' => $db->table('mcq_tbl_mcqstempsaved')->where('author_id', $context['authorId'])->where('status', 0)->orderBy('mcq_id', 'DESC')->get()->getResultArray(),
            'programs' => $this->assignedPrograms($db, $context['authorId'], (int) $context['author']['subject_id'], $db->table('mcq_tbl_subject')->where('subject_id', (int) $context['author']['subject_id'])->get()->getRowArray() ?? []),
            'hasPrograms' => $db->tableExists('mcq_tbl_programs'),
            'hasProgramAssignments' => $db->tableExists('mcq_tbl_faculty_subject_program'),
            'errors' => [],
            'success' => null,
            'editing' => $row,
        ]);
    }

    /** Return only active programs explicitly assigned to this faculty + subject. */
    private function assignedPrograms($db, int $authorId, int $subjectId, array $subject): array
    {
        if (! $db->tableExists('mcq_tbl_programs') || ! $db->tableExists('mcq_tbl_faculty_subject_program')) {
            return [];
        }
        $mapFields = $db->getFieldNames('mcq_tbl_faculty_subject_program');
        $programFields = $db->getFieldNames('mcq_tbl_programs');
        foreach (['author_id', 'progId', 'subject_id', 'department_id', 'is_active'] as $requiredField) {
            if (! in_array($requiredField, $mapFields, true)) return [];
        }
        if (! in_array('progId', $programFields, true) || ! in_array('progName', $programFields, true)) return [];

        $builder = $db->table('mcq_tbl_faculty_subject_program m')
            ->select('DISTINCT p.progId, p.progName', false)
            ->join('mcq_tbl_programs p', 'p.progId = m.progId')
            ->where('m.author_id', $authorId)
            ->where('m.subject_id', $subjectId)
            ->where('m.is_active', 1);
        if (isset($subject['department_id'])) $builder->where('m.department_id', (int) $subject['department_id']);
        if (in_array('is_Active', $programFields, true)) $builder->where('p.is_Active', 1);
        return $builder->orderBy('p.progName')->get()->getResultArray();
    }

    public function systemsBySubject(): ResponseInterface
    {
        $context = $this->facultyContext();
        if ($context instanceof \CodeIgniter\HTTP\RedirectResponse) return $this->response->setStatusCode(403)->setJSON(['message' => 'Not authorized.']);

        $db = $context['db'];
        $subjectId = (int) $context['author']['subject_id'];
        $disciplineId = (int) $this->request->getGet('subdiscipline_id');
        $fields = $db->getFieldNames('mcq_tbl_system');
        $builder = $db->table('mcq_tbl_system')->select('system_id AS id, system AS name')->where('subject_id', $subjectId);
        $hasDiscs = $db->tableExists('mcq_tbl_subdiscipline') && $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->countAllResults() > 0;
        if (in_array('subdiscipline_id', $fields, true)) {
            if ($hasDiscs) {
                if (!$disciplineId) return $this->response->setJSON(['systems' => [], 'hasSubDisciplines' => true]);
                $valid = $db->table('mcq_tbl_subdiscipline')->where('subject_id', $subjectId)->where('subdiscipline_id', $disciplineId)->countAllResults() > 0;
                if (!$valid) return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid Sub Discipline.']);
                $builder->where('subdiscipline_id', $disciplineId);
            } else {
                $builder->where('subdiscipline_id', null);
            }
        }
        return $this->response->setJSON(['systems' => $builder->orderBy('system')->get()->getResultArray(), 'hasSubDisciplines' => $hasDiscs]);
    }

    public function topicsBySystem(int $systemId): ResponseInterface
    {
        $context = $this->facultyContext();
        if ($context instanceof \CodeIgniter\HTTP\RedirectResponse) return $this->response->setStatusCode(403)->setJSON(['message' => 'Not authorized.']);
        $db = $context['db'];
        $subjectId = (int) $context['author']['subject_id'];
        $validSystem = $db->table('mcq_tbl_system')->where('system_id', $systemId)->where('subject_id', $subjectId)->countAllResults() > 0;
        if (!$validSystem) return $this->response->setStatusCode(403)->setJSON(['message' => 'System is outside your assigned subject.']);
        $items = $db->table('mcq_tbl_topic t')->select('t.topic_id AS id, t.topic AS name')
            ->join('mcq_tbl_system s', 's.system_id = t.system_id')
            ->where('t.system_id', $systemId)->where('s.subject_id', $subjectId)->orderBy('t.topic')->get()->getResultArray();
        return $this->response->setJSON(['topics' => $items]);
    }
}
