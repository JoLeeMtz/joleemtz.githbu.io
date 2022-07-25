<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class FileManager extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function postIndex()
    {
        $userModel = new UserModel();
        $loggedUserId = session()->get('loggedUserId');
        $userInfo = $userModel->find($loggedUserId);
        // Setup data for insert query
        $data += [
            'title' => 'FileManager',
            'userInfo' => $userInfo
        ];
        return view('FileManager/index', $data);
    }

    public function getIndex()
    {
        $loggedUserId = session()->get('loggedUserId');
        // Check if user connected
        if (isset($loggedUserId))
        {
            $userModel = new UserModel();
            $userInfo = $userModel->find($loggedUserId);
            // Setup data for insert query
            $data = [
                'title' => 'FileManager',
                'userInfo' => $userInfo
            ];
            return view('FileManager/index', $data);
        }
        else
        {
            return view('errors/html/error_404');
        }
    }

    /* LoggedUser - uploadFile ********************************************* */
    public function postUploadFile() 
    {
        if (!isset($data))
        {
            $userModel = new UserModel();
            $loggedUserId = session()->get('loggedUserId');
            $userInfo = $userModel->find($loggedUserId);
            $data = [
                'userInfo' => $userInfo
            ];
        }

        try 
        {
            $loggedUserId = session()->get('loggedUserId');
            $file = $this->request->getFile('fileUploader');

            if ($loggedUserId && $file->isValid() && !$file->hasMoved())
            {
                if ($file->move('uploads'))
                {
                    $data += [
                        'fileName' => $file,
                        'notification' => 'File uploaded in server.'
                    ];
                }
                else
                {
                    $data += [
                        'notification' => $file->getErrorString()
                    ];
                }
                return view('FileManager/index', $data);
            }
            else
            {
                $data += [
                    'notification' => 'The file has already been moved.'
                ];
                return view('FileManager/index', $data);
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return view('FileManager/index', $data);
        }
    }
/* ************************************************************************* */

/* LoggedUser - deleteFile ************************************************* */
public function postDeleteFile() 
{
    if (!isset($data))
    {
        $userModel = new UserModel();
        $loggedUserId = session()->get('loggedUserId');
        $userInfo = $userModel->find($loggedUserId);
        $data = [
            'userInfo' => $userInfo
        ];
    }
    $fileName = $this->request->getPost('fileName');
    unlink(getcwd() . '\uploads\\' . $fileName);
    $data += [
        'notification' => 'The file has been deleted.'
    ];
    return view('FileManager/index', $data);
}
/* ************************************************************************* */

/* LoggedUser - downloadFile *********************************************** */
public function postDownloadFile()
{
    if (!isset($data))
    {
        $userModel = new UserModel();
        $loggedUserId = session()->get('loggedUserId');
        $userInfo = $userModel->find($loggedUserId);
        $data = [
            'userInfo' => $userInfo
        ];
    }/**/
    // Get POST informations from submit
    $fileName = $this->request->getPost('fileName');
    $filePath = 'uploads/' . $fileName;

    if (!empty($fileName) && file_exists($filePath))
    {
        header("Cache-Control: public");
        header("Cache-Description: File Transfer");
        header("Cache-Disposition: attachment; filename=$fileName");
        header("Cache-Type: application/zip");
        header("Cache-Transfer-Encoding: binary");
        readfile($filePath);
        $data += [
            "notification" => "File downloaded."
        ];
    }
    else
    {
        $data += [
            "notification" => "File didn't download."
        ];
    }
    /*if (file_get_contents(file_get_contents(base_url() . '/public/uploads/' . $fileName)))
    {
        $data += [
            "notification" => "File downloaded."
        ];
    }
    else
    {
        $data += [
            "notification" => "File didn't download."
        ];
    }*/
    return view('FileManager/index', $data);
}

public function getDownload()
{
    if (!isset($data))
    {
        $userModel = new UserModel();
        $loggedUserId = session()->get('loggedUserId');
        $userInfo = $userModel->find($loggedUserId);
        $data = [
            'userInfo' => $userInfo
        ];
    }
    if (!empty($_GET['filename']))
    {
        // Get POST informations from submit
        $fileName = $_GET['filename'];//$this->request->getPost('fileName');
        $filePath = 'uploads/' . $fileName;

        if (!empty($fileName) && file_exists($filePath))
        {
            header("Cache-Control: public");
            header("Cache-Description: File Transfer");
            header("Cache-Disposition: attachment; filename=$fileName");
            //header("Cache-Type: " . mime_content_type('test.txt'));
            header("Cache-Transfer-Encoding: binary");
            flush();
            readfile($filePath);
            $data += [
                "notification" => "File downloaded."
            ];
        }
        else
        {
            $data += [
                "notification" => "File didn't download."
            ];
        }
    }
    else
    {
        $data += [
            "notification" => "Not a valid file provided."
        ];
    }
    $fileContent = $this->response->download($filePath, null);
    return file_put_contents($fileName, $fileContent);
    return view('FileManager/index', $data);
}
/* ************************************************************************* */
}
