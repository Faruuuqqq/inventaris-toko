<?php

namespace App\Controllers;

use CodeIgniter\Model;

/**
 * BaseCRUDController - Abstract base class for Master data controllers
 *
 * Provides common CRUD operations to reduce code duplication.
 * Extend this class and implement the abstract methods to use.
 */
abstract class BaseCRUDController extends BaseController
{
    protected Model $model;

    protected string $viewPath;

    protected string $routePath;

    protected string $entityName;

    protected string $entityNamePlural;

    /**
     * Get the model instance - must be implemented by child class
     */
    abstract protected function getModel(): Model;

    /**
     * Get validation rules for store operation
     */
    abstract protected function getStoreValidationRules(): array;

    /**
     * Get validation rules for update operation
     */
    protected function getUpdateValidationRules(int|string $id): array
    {
        return $this->getStoreValidationRules();
    }

    /**
     * Get data from request for store/update
     */
    abstract protected function getDataFromRequest(): array;

    /**
     * Get data for index view - can be overridden for custom queries
     */
    protected function getIndexData(): array
    {
        return $this->model->findAll();
    }

    /**
     * Get additional data for index view
     */
    protected function getAdditionalViewData(): array
    {
        return [];
    }

    /**
     * Initialize controller
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    protected function isAjax(): bool
    {
        return $this->request->isAJAX() || $this->request->getHeader('X-Requested-With')?->getValue() === 'XMLHttpRequest';
    }

    protected function handleAccessDenied(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->isAjax()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses',
            ]);
        }
        return redirect()->back()->with('error', 'Anda tidak memiliki akses');
    }

    protected function handleValidationError(string $message = 'Terjadi kesalahan validasi. Silakan periksa kembali data Anda.'): \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->isAjax()) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => $message,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    protected function handleSuccess(string $message, int $code = 200, array $extraData = []): \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->isAjax()) {
            return $this->response->setStatusCode($code)->setJSON(array_merge([
                'success' => true,
                'message' => $message,
            ], $extraData));
        }
        return redirect()->to($this->routePath)->with('success', $message);
    }

    protected function handleError(\Exception $e): \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
    {
        log_message('error', $e->getMessage());
        if ($this->isAjax()) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }

    /**
     * Display list of all records
     */
    public function index()
    {
        $data = array_merge([
            'title' => $this->entityName,
            'subtitle' => 'Kelola data ' . strtolower($this->entityName),
            strtolower($this->entityNamePlural) => $this->getIndexData(),
        ], $this->getAdditionalViewData());

        return view($this->viewPath . '/index', $data);
    }

    /**
     * Store a new record
     */
    public function store()
    {
        if (!$this->checkStoreAccess()) {
            return $this->handleAccessDenied();
        }

        $rules = $this->getStoreValidationRules();
        if (!$this->validate($rules)) {
            return $this->handleValidationError();
        }

        try {
            $data = $this->getDataFromRequest();
            $data = $this->beforeStore($data);

            $this->model->insert($data);
            $insertId = $this->model->getInsertID();

            $this->afterStore($insertId);

            return $this->handleSuccess(
                $this->entityName . ' berhasil ditambahkan',
                201,
                ['id' => $insertId]
            );
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Update an existing record
     */
    public function update($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return $this->handleAccessDenied();
        }

        $rules = $this->getUpdateValidationRules($id);
        if (!$this->validate($rules)) {
            return $this->handleValidationError();
        }

        try {
            $data = $this->getDataFromRequest();
            $data = $this->beforeUpdate($id, $data);

            $this->model->update($id, $data);

            $this->afterUpdate($id);

            return $this->handleSuccess($this->entityName . ' berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Delete a record
     */
    public function delete($id)
    {
        if (!$this->checkDeleteAccess($id)) {
            return $this->handleAccessDenied();
        }

        if (!$this->canDelete($id)) {
            $message = $this->entityName . ' tidak dapat dihapus karena masih memiliki data terkait';
            if ($this->isAjax()) {
                return $this->response->setStatusCode(409)->setJSON([
                    'success' => false,
                    'message' => $message,
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        try {
            $this->beforeDelete($id);

            $this->model->delete($id);

            $this->afterDelete($id);

            return $this->handleSuccess($this->entityName . ' berhasil dihapus');
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge([
            'title' => 'Tambah ' . $this->entityName,
            'subtitle' => 'Tambahkan data ' . strtolower($this->entityName) . ' baru',
        ], $this->getAdditionalViewData());

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', $this->entityName . ' tidak ditemukan');
        }

        $data = array_merge([
            'title' => 'Edit ' . $this->entityName,
            'subtitle' => 'Ubah data ' . strtolower($this->entityName),
            strtolower($this->entityName) => $record,
        ], $this->getAdditionalViewData());

        return view($this->viewPath . '/edit', $data);
    }

    public function getList()
    {
        $selectFields = $this->getListSelectFields();
        $items = $this->model
            ->select($selectFields)
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->response->setStatusCode(200)->setJSON($items);
    }

    protected function getListSelectFields(): string
    {
        return 'id, code, name';
    }

    /**
     * Check if user has access to store - override if needed
     */
    protected function checkStoreAccess(): bool
    {
        return true;
    }

    /**
     * Check if user has access to update - override if needed
     */
    protected function checkUpdateAccess($id): bool
    {
        return true;
    }

    /**
     * Check if user has access to delete - override if needed
     */
    protected function checkDeleteAccess($id): bool
    {
        return true;
    }

    /**
     * Check if record can be deleted - override to add business logic
     */
    protected function canDelete($id): bool
    {
        return true;
    }

    /**
     * Hook before store - override to modify data
     */
    protected function beforeStore(array $data): array
    {
        return $data;
    }

    /**
     * Hook after store - override for post-insert actions
     */
    protected function afterStore($insertId): void
    {
        // Override if needed
    }

    /**
     * Hook before update - override to modify data
     */
    protected function beforeUpdate($id, array $data): array
    {
        return $data;
    }

    /**
     * Hook after update - override for post-update actions
     */
    protected function afterUpdate($id): void
    {
        // Override if needed
    }

    /**
     * Hook before delete - override for pre-delete actions
     */
    protected function beforeDelete($id): void
    {
        // Override if needed
    }

    /**
     * Hook after delete - override for post-delete actions
     */
    protected function afterDelete($id): void
    {
        // Override if needed
    }
}
