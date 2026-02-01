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
        // Check access if required
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        // Validate
        $rules = $this->getStoreValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Get data and insert
            $data = $this->getDataFromRequest();
            $data = $this->beforeStore($data);

            $this->model->insert($data);

            $this->afterStore($this->model->getInsertID());

            return redirect()
                ->to($this->routePath)
                ->with('success', $this->entityName . ' berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing record
     */
    public function update($id)
    {
        // Check access if required
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        // Validate
        $rules = $this->getUpdateValidationRules($id);
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Get data and update
            $data = $this->getDataFromRequest();
            $data = $this->beforeUpdate($id, $data);

            $this->model->update($id, $data);

            $this->afterUpdate($id);

            return redirect()
                ->to($this->routePath)
                ->with('success', $this->entityName . ' berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete a record
     */
    public function delete($id)
    {
        // Check access if required
        if (!$this->checkDeleteAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        // Check if can be deleted
        if (!$this->canDelete($id)) {
            return redirect()->back()->with('error', $this->entityName . ' tidak dapat dihapus karena masih memiliki data terkait');
        }

        try {
            $this->beforeDelete($id);

            $this->model->delete($id);

            $this->afterDelete($id);

            return redirect()
                ->to($this->routePath)
                ->with('success', $this->entityName . ' berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
