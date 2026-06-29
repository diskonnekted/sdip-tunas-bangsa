<?php
session_start();
if (!isset($_SESSION['parent_user_id']) || !isset($_SESSION['parent_role']) || $_SESSION['parent_role'] !== 'orang_tua') {
    header('Location: login.php');
    exit;
}
$active_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#dc2626">
    <title>7KAIH - <?= htmlspecialchars($_SESSION['parent_full_name']) ?></title>
    
    <!-- PWA Settings -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f3f4f6; 
            -webkit-tap-highlight-color: transparent; 
            overscroll-behavior-y: none;
        }
        .app-container { 
            max-width: 480px; 
            margin: 0 auto; 
            min-height: 100vh; 
            background-color: #f9fafb; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1); 
            position: relative;
            padding-bottom: 70px; /* Space for bottom nav */
        }
        
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        
        /* Bottom Nav styling */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: white;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            padding-bottom: env(safe-area-inset-bottom); /* For iPhone X+ */
            z-index: 50;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            flex: 1;
            color: #9ca3af; /* text-gray-400 */
            transition: all 0.3s ease;
        }
        .nav-item.active {
            color: #dc2626; /* text-red-600 */
        }
        .nav-item i { font-size: 1.25rem; margin-bottom: 4px; }
        .nav-item span { font-size: 0.65rem; font-weight: 600; }
        
        /* Header App Bar */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 16px 20px;
            padding-top: max(16px, env(safe-area-inset-top));
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }
    </style>
</head>
<body class="antialiased">
    <div class="app-container">
        <!-- App Header -->
        <header class="app-header">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="font-bold text-lg leading-tight">7KAIH</h1>
                    <p class="text-xs text-red-100 opacity-90 truncate w-48">Hi, <?= htmlspecialchars($_SESSION['parent_full_name']) ?></p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
        </header>
        
        <!-- Main Content Area -->
        <main class="p-4">
