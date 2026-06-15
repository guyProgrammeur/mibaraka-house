<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Erreur') - EcoPrime Sign</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background-color: #f1f2f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-card {
            background: white;
            border: 1px solid #c6ccd2;
            overflow: hidden;
        }
        
        .error-header {
            background-color: #2d3339;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.05);
            line-height: 1;
            position: absolute;
            top: 20px;
            right: 20px;
            user-select: none;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .error-title {
            color: white;
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .error-message {
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
        }
        
        .error-body {
            padding: 40px 30px;
        }
        
        .error-description {
            color: #5b6671;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: #2d3339;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #17191c;
        }
        
        .btn-secondary {
            background-color: #e3e6e8;
            color: #2d3339;
            border-color: #c6ccd2;
        }
        
        .btn-secondary:hover {
            background-color: #c6ccd2;
        }
        
        .error-help {
            border-top: 1px solid #e3e6e8;
            padding-top: 30px;
            margin-top: 10px;
        }
        
        .error-help h4 {
            color: #2d3339;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .contact-info {
            background-color: #f1f2f4;
            padding: 15px;
            text-align: center;
        }
        
        .contact-info p {
            color: #5b6671;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .contact-info a {
            color: #2d3339;
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
            color: #17191c;
        }
        
        @media (max-width: 640px) {
            .error-code {
                font-size: 80px;
            }
            
            .error-header {
                padding: 30px 20px;
            }
            
            .error-title {
                font-size: 24px;
            }
            
            .error-body {
                padding: 30px 20px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-header">
                <div class="error-code">@yield('code')</div>
                <div class="error-icon">
                    @yield('icon')
                </div>
                <h1 class="error-title">@yield('title')</h1>
                <p class="error-message">@yield('message')</p>
            </div>
            
            <div class="error-body">
                <div class="error-description">
                    @yield('description')
                </div>
                
                <div class="error-actions">
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Retour
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Accueil
                    </a>
                </div>
                
                <div class="error-help">
                    <h4>Besoin d'aide ?</h4>
                    <div class="contact-info">
                        <p>Contactez notre support technique</p>
                        <a href="mailto:support@ecoprime.cd">support@ecoprime.cd</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>