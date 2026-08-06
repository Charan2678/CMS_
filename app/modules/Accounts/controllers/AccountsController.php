<?php

declare(strict_types=1);

namespace App\Modules\Accounts\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Accounts\services\AccountsService;

class AccountsController extends Controller
{
    private AccountsService $accountsService;

    public function __construct()
    {
        $this->accountsService = new AccountsService();
    }

    public function index(): void
    {
        Permission::enforce('accounts.manage');

        $staffPayroll = $this->accountsService->getStaffPayroll(1);

        $this->render('Accounts/views/index', [
            'title'        => 'Accounts & Payroll Ledger',
            'staffPayroll' => $staffPayroll,
        ], 'layout');
    }
}
