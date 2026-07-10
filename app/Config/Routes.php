<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ===== ROOT =====
$routes->get('/', static function () {
    if (auth()->loggedIn()) {
        $user = auth()->user();
        if ($user->inGroup('superadmin', 'admin')) {
            return redirect()->to('/admin');
        }
        return redirect()->to('/user/dashboard');
    }
    return redirect()->to('/login');
});

// ===== SHIELD AUTH ROUTES =====
service('auth')->routes($routes);

// ===== READ LOGS =====
$routes->get('read-logs', 'Admin\AnalyticsController::readLogs');

// ===== LANDING PAGE (PÚBLICA) =====
$routes->get('v/(:segment)/(:segment)', 'LandingController::show/$1/$2');

// ===== LOGIN POR TOKEN (PÚBLICO) =====
$routes->get('login-token/(:segment)', 'User\UserDashboardController::loginByToken/$1');

// ===== API PÚBLICA (sem auth) =====
$routes->group('api', function ($routes) {
    $routes->post('track', 'TrackingController::store');
    $routes->post('track/geo', 'TrackingController::storeGeo');
    $routes->post('viralize', 'ViralizeController::create');
});

// ===== ADMIN (Shield Protected - Group Filter) =====
$routes->group('admin', ['filter' => 'group:superadmin,admin'], function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');

    // Campanhas CRUD
    $routes->get('campaigns', 'Admin\CampaignController::index');
    $routes->get('campaigns/create', 'Admin\CampaignController::create');
    $routes->post('campaigns', 'Admin\CampaignController::store');
    $routes->get('campaigns/(:segment)/edit', 'Admin\CampaignController::edit/$1');
    $routes->post('campaigns/(:segment)/update', 'Admin\CampaignController::update/$1');
    $routes->post('campaigns/(:segment)/delete', 'Admin\CampaignController::delete/$1');
    $routes->post('campaigns/(:segment)/toggle-status', 'Admin\CampaignController::toggleStatus/$1');

    // Editor de Mensagens
    $routes->get('campaigns/(:segment)/messages', 'Admin\MessageController::editor/$1');
    $routes->post('campaigns/(:segment)/messages', 'Admin\MessageController::save/$1');
    $routes->post('campaigns/(:segment)/upload', 'Admin\MessageController::upload/$1');

    // Analytics
    $routes->get('campaigns/(:segment)/analytics', 'Admin\AnalyticsController::overview/$1');
    $routes->get('campaigns/(:segment)/analytics/graph', 'Admin\AnalyticsController::graph/$1');
    $routes->get('campaigns/(:segment)/analytics/map', 'Admin\AnalyticsController::map/$1');
    $routes->get('campaigns/(:segment)/analytics/leads', 'Admin\AnalyticsController::leads/$1');
    $routes->get('campaigns/(:segment)/analytics/export', 'Admin\AnalyticsController::export/$1');

    // API Admin (JSON para JS)
    $routes->get('api/campaigns/(:segment)/propagators', 'Admin\AnalyticsController::propagatorsJson/$1');
    $routes->get('api/campaigns/(:segment)/events', 'Admin\AnalyticsController::eventsJson/$1');
});

// ===== USER DASHBOARD (Shield Protected - Leads) =====
$routes->group('user', ['filter' => 'session'], function ($routes) {
    $routes->get('dashboard', 'User\UserDashboardController::index');
    $routes->get('api/network', 'User\UserDashboardController::networkJson');
});
