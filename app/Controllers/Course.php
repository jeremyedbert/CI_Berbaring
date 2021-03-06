<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CourseModel;
use App\Models\MentorActivityModel;
use App\Models\StudentActivityModel;
use App\Models\SubchapterModel;

class Course extends BaseController
{
    public function __construct()
    {
        $this->categories = new CategoryModel();
        $this->courses = new CourseModel();
    }

    private function generateErrorToView($validation)
    {
        if ($validation->hasError('c_name')) {
            session()->setFlashdata('error_c_name', $validation->getError('c_name'));
        }

        if ($validation->hasError('c_desc')) {
            session()->setFlashdata('error_c_desc', $validation->getError('c_desc'));
        }

        if ($validation->hasError('category_id')) {
            session()->setFlashdata('error_category_id', $validation->getError('category_id'));
        }
    }

    public function index()
    {
        $data['categories'] = $this->categories->findAll();
        return view('add_course', $data);
    }

    public function add()
    {
        $validation = \Config\Services::validation();

        $validation->setRules($this->courses->validationRules, $this->courses->errorMessage);

        $isValid = $validation->withRequest($this->request)->run();

        $isPaidCourse = $this->request->getPost('paid_check'); //0 = false, 1 = true

        if ($isPaidCourse != null) { //validasi paid check

            if ($isPaidCourse && $this->request->getPost('c_price') < 1) {
                session()->setFlashdata('error_c_price', 'Harga Course harus lebih dari 0');

                $isValid = false;
            }
        } else {
            session()->setFlashdata('error_paid_check', 'Jenis Course harus ditentukan!');

            $isValid = false;
        }

        if (!$this->validate([
            'course_picture' => [
                'rules' => 'uploaded[course_picture]|mime_in[course_picture,image/jpg,image/jpeg,image/png]|max_size[course_picture,2048]',
                'errors' => [
                    'uploaded' => 'Harus Ada File yang diupload',
                    'mime_in' => 'Format File Harus Berupa jpg,jpeg,png',
                    'max_size' => 'Ukuran File Maksimal 2 MB'
                ]
            ]
        ])) {

            session()->setFlashdata('error_course_picture_1', $this->validator->getError('course_picture'));

            $isValid = false;
        }

        if ($this->request->getFile('course_picture') == null || !$this->request->getFile('course_picture')->isValid()) {

            session()->setFlashdata('error_course_picture_2', 'terjadi kesalahan upload gambar');

            $isValid = false;
        }

        if ($isValid) {

            $file = $this->request->getFile('course_picture');

            $fileName = $file->getRandomName();

            $c_id = (int)round(gettimeofday(true));

            $file->move(ROOTPATH . 'public/uploads/' . $c_id, $fileName);

            $db = db_connect();

            $c_name = $db->escape($this->request->getPost('c_name'));
            $c_desc = $db->escape($this->request->getPost('c_desc'));
            $c_price = ($isPaidCourse) ? $this->request->getPost('c_price') : 0;
            $c_imagepath = $db->escape($fileName);
            $category_id = $this->request->getPost('category_id');
            $user_id = session()->get('id');

            $this->courses->saveCourse($c_id, $c_name, $c_imagepath, $c_desc, $c_price, $category_id, $user_id);

            return redirect()->to(base_url('course/' . $c_id . '/info'));
        } else {
            $this->generateErrorToView($validation);

            return redirect()->back()->withInput();
        }
    }

    public function info($c_id)
    {
        $subchapters = new SubchapterModel();

        $mentorActivities = new MentorActivityModel();

        $mentorActivity = $mentorActivities->getCouserCreatorId($c_id);

        if (session()->get('id') == $mentorActivity->user_id) {

            $data['course'] = $this->courses->info($c_id);

            $data['course']['published_date'] = $mentorActivities->getCoursePublishedDate($c_id)->published_date;

            $data['subchapters'] = $subchapters->where('c_id', $c_id)->findAll();

            return view('info_course', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }


    public function search()
    {
        $query = $this->request->getVar('query');

        $data['query'] = $query;

        $data['course'] = $this->courses->searchc($query);

        return view('search_course', $data);
    }

    public function landing()
    {
        $data['course'] = $this->courses->landing();
        return view('landing_page', $data);
    }

    public function edit($c_id)
    {
        $mentorActivities = new MentorActivityModel();

        $mentorActivity = $mentorActivities->getCouserCreatorId($c_id);

        if (session()->get('id') == $mentorActivity->user_id) {

            $data['course'] = $this->courses->where('c_id', $c_id)->first();
            $data['categories'] = $this->categories->findAll();
            return view('edit_course', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($c_id)
    {
        $mentorActivities = new MentorActivityModel();

        $mentorActivity = $mentorActivities->getCouserCreatorId($c_id);

        if (session()->get('id') == $mentorActivity->user_id) {
            $validation = \Config\Services::validation();

            $validation->setRules($this->courses->validationRules, $this->courses->errorMessage);

            $isValid = $validation->withRequest($this->request)->run();

            $isPaidCourse = $this->request->getPost('paid_check'); //0 = false, 1 = true

            if ($isPaidCourse != null) { //validasi paid check

                if ($isPaidCourse && $this->request->getPost('c_price') < 1) {
                    session()->setFlashdata('error_c_price', 'Harga Course harus lebih dari 0');

                    $isValid = false;
                }
            } else {
                session()->setFlashdata('error_paid_check', 'Jenis Course harus ditentukan!');

                $isValid = false;
            }

            if ($this->request->getFile('course_picture')->isValid()) {
                if (!$this->validate([
                    'course_picture' => [
                        'rules' => 'uploaded[course_picture]|mime_in[course_picture,image/jpg,image/jpeg,image/png]|max_size[course_picture,2048]',
                        'errors' => [
                            'uploaded' => 'Harus Ada File yang diupload',
                            'mime_in' => 'Format File Harus Berupa jpg,jpeg,png',
                            'max_size' => 'Ukuran File Maksimal 2 MB'
                        ]
                    ]
                ])) {

                    session()->setFlashdata('error_course_picture_1', $this->validator->getError('course_picture'));

                    $isValid = false;
                }


                if ($isValid) {

                    $file = $this->request->getFile('course_picture');

                    $fileName = $file->getRandomName();

                    $file->move(ROOTPATH . 'public/uploads/' . $c_id, $fileName);

                    $data['c_name'] = $this->request->getPost('c_name');
                    $data['c_desc'] = $this->request->getPost('c_desc');
                    $data['c_price'] = ($isPaidCourse) ? $this->request->getPost('c_price') : 0;
                    $data['c_imagepath'] = $fileName;
                    $data['category_id'] = $this->request->getPost('category_id');

                    $this->courses->update($c_id, $data);

                    return redirect()->to(base_url('course/' . $c_id . '/info'));
                }
            } else {
                if ($isValid) {
                    $data['c_name'] = $this->request->getPost('c_name');
                    $data['c_desc'] = $this->request->getPost('c_desc');
                    $data['c_price'] = ($isPaidCourse) ? $this->request->getPost('c_price') : 0;
                    $data['category_id'] = $this->request->getPost('category_id');

                    $this->courses->update($c_id, $data);

                    return redirect()->to(base_url('course/' . $c_id . '/info'));
                }
            }

            $this->generateErrorToView($validation);

            return redirect()->back()->withInput();
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function publish($c_id)
    {
        $mentorActivities = new MentorActivityModel();

        $mentorActivity = $mentorActivities->getCouserCreatorId($c_id);

        if (session()->get('id') == $mentorActivity->user_id) {

            if ($this->courses->publishCourse($c_id) > 0) {
                session()->setFlashdata('published_message', 'Publish Course Berhasil!');

                return redirect()->to('/homepage/mentor');
            } else {
                session()->setFlashdata('published_message', 'Publish Course Gagal!');

                return redirect()->back();
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function course_page($c_id)
    {

        $student = new StudentActivityModel();
        $subchapters = new SubchapterModel();
        $session = session();

        //check sudah beli belum
        if ($session->get('is_logged_in')) {
            $check_query = $student->query('SELECT count(*) AS jumlah FROM student_activity WHERE c_id = ' . $c_id . ' AND user_id = ' . $session->get('id'));
            $check_data =  $check_query->getRowArray();
        } else {
            $check_data['jumlah'] = 0;
        }
        if ($session->get('is_logged_in') == false || $check_data['jumlah'] == 0) {
            $data['course'] = $this->courses->getCourseDetail($c_id);
            $data['chapters'] = $subchapters->getChapters($c_id);
            $data['mode'] = 1; //sudah beli

            if ($data['course'] != NULL) {
                return view('course_learning_page.php', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            $data['course'] = $this->courses->getCourseDetail($c_id);
            $data['chapters'] = $subchapters->getChapters($c_id);
            $data['mode'] = 2; //belum beli

            if ($data['course'] != NULL) {
                return view('course_learning_page.php', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function course_subchapter_page($c_id, $sc_id)
    {
        $student = new StudentActivityModel();
        $subchapters = new SubchapterModel();
        $session = session();

        //check sudah beli belum
        if ($session->get('is_logged_in')) {
            $check_query = $student->query('SELECT count(*) AS jumlah FROM student_activity WHERE c_id = ' . $c_id . ' AND user_id = ' . $session->get('id'));
            $check_data =  $check_query->getRowArray();
        } else {
            $check_data['jumlah'] = 0;
        }

        if ($check_data['jumlah'] != 0) {
            $data['course'] = $this->courses->getCourseDetail($c_id);
            $data['chapters'] = $subchapters->getChapters($c_id);
            $data['subchapter'] = $data['chapters'][$sc_id - 1];
            $data['no'] = $sc_id;
            $data['mode'] = 3;

            if ($data['subchapter'] != NULL and $data['course'] != NULL) {
                return view('course_learning_page.php', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            return redirect()->to(base_url('course/' . $c_id));
        }
    }
}
