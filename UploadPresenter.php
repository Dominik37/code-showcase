<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Http\Request;
use Nette\Utils\FileSystem;
use App\Model\Model;

/**
 * Handles AJAX file uploads
 *
 * @since 1.0.2
 * @package App\Presenters
 */
class UploadPresenter extends BasePresenter
{
    private const
        TEMP_FOLDER = 'files/tmp/',
        TEMP_TABLE = 'tmp_email_form';

    private Model $model;

    /**
     * UploadPresenter constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        parent::__construct();
        $this->model = $model;
    }
    
    public function startup(): void
    {
        parent::startup();
        $this->store($this->getHttpRequest());
    }

    /**
     * Stores unique ID for temporary file location
     *
     * @param Request $request HTTP request
     */
    public function store(Request $request): void
    {
        $file = $request->getFile('files');
        if (($file !== null) && $file->isOk()) {
            $fileName = time() . '-' . $file->getName();
            $sessionId = $this->session->getId();
            $folder = uniqid('', true) . '-' . time();
            $path = self::TEMP_FOLDER . $folder . '/';

            FileSystem::createDir(self::TEMP_FOLDER . $folder);
            $file->move($path . $fileName);

            $this->model->insertFile(self::TEMP_TABLE, $fileName, $path, $sessionId);

            echo $folder;
        }

        echo '';
    }

    public function renderDelete(): void
    {
        $this->delete();
    }

    /**
     * Deletes all uploaded temporary files
     */
    public function delete(): void
    {
        $sessionId = $this->session->getId();
        $tmpFiles = $this->model->getFiles(self::TEMP_TABLE, $sessionId);
        foreach ($tmpFiles as $tmpFile) {
            FileSystem::delete($tmpFile->path);
        }
        $this->model->deleteTempFiles($sessionId);
    }
}
