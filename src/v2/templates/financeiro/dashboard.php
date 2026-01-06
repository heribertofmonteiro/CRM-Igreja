<?php
/**
 * Financeiro Dashboard
 * URL: /v2/financeiro
 */
use ChurchCRM\Authentication\AuthenticationManager;
?>
<!DOCTYPE html>
<html>
<head>
    <?php include __DIR__ . '/../../Include/Header-HTML-Scripts.php'; ?>
    <title><?= $sPageTitle ?> - <?= $sRootPath ?></title>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include __DIR__ . '/../../Include/Header.php'; ?>
        <?php include __DIR__ . '/../../Include/Left-Sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-money-bill-wave"></i> <?= gettext('Financeiro') ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= $sRootPath ?>/v2/dashboard"><?= gettext('Dashboard') ?></a></li>
                                <li class="breadcrumb-item active"><?= gettext('Financeiro') ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Depósitos -->
                        <?php if ($bEnabledFinance): ?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-cash-register"></i> <?= gettext('Depósitos') ?>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/FindDepositSlip.php" class="nav-link">
                                                <i class="fas fa-list"></i> <?= gettext('View All Deposits') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/FinancialReports.php" class="nav-link">
                                                <i class="fas fa-chart-line"></i> <?= gettext('Deposit Reports') ?>
                                            </a>
                                        </li>
                                        <?php if (isset($_SESSION['iCurrentDeposit']) && $_SESSION['iCurrentDeposit'] > 0): ?>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/DepositSlipEditor.php?DepositSlipID=<?= $_SESSION['iCurrentDeposit'] ?>" class="nav-link">
                                                <i class="fas fa-edit"></i> <?= gettext('Edit Deposit Slip') ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Arrecadações de Fundos (Fundraisers) -->
                        <?php if ($bEnabledFundraiser): ?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-money-bill-alt"></i> <?= gettext('Fundraiser') ?>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/FundRaiserEditor.php?FundRaiserID=-1" class="nav-link">
                                                <i class="fas fa-plus"></i> <?= gettext('Create New Fundraiser') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/FindFundRaiser.php" class="nav-link">
                                                <i class="fas fa-list"></i> <?= gettext('View All Fundraisers') ?>
                                            </a>
                                        </li>
                                        <?php if (isset($_SESSION['iCurrentFundraiser']) && $_SESSION['iCurrentFundraiser'] > 0): ?>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/FundRaiserEditor.php?FundRaiserID=<?= $_SESSION['iCurrentFundraiser'] ?>" class="nav-link">
                                                <i class="fas fa-edit"></i> <?= gettext('Edit Fundraiser') ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/AddDonors.php" class="nav-link">
                                                <i class="fas fa-user-plus"></i> <?= gettext('Add Donors to Buyer List') ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $sRootPath ?>/PaddleNumList.php" class="nav-link">
                                                <i class="fas fa-users"></i> <?= gettext('View Buyers') ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Relatórios e Dados -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar"></i> <?= gettext('Relatórios e Dados') ?>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if ($bEnabledFinance): ?>
                                        <div class="col-md-6">
                                            <h5><?= gettext('Financial Reports') ?></h5>
                                            <ul class="nav nav-pills flex-column">
                                                <li class="nav-item">
                                                    <a href="<?= $sRootPath ?>/FinancialReports.php" class="nav-link">
                                                        <i class="fas fa-file-invoice-dollar"></i> <?= gettext('Financial Reports') ?>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="<?= $sRootPath ?>/TaxReport.php" class="nav-link">
                                                        <i class="fas fa-receipt"></i> <?= gettext('Tax Report') ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include __DIR__ . '/../../Include/Footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../../Include/Footer-HTML-Scripts.php'; ?>
</body>
</html>

