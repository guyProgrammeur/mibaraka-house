<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affiche QR Code - {{ $qrCode->name }}</title>
    <style>
        /* Configuration de la page A4 pour DomPDF */
        @page {
            size: a4 portrait;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: {{ $qrCode->poster_background_color ?? '#FFFFFF' }};
            color: {{ $qrCode->poster_text_color ?? '#1a1a1a' }};
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Conteneur Principal / Format de l'affiche */
        .poster-container {
            position: relative;
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            padding: 50px 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: {{ $qrCode->poster_background_color ?? '#FFFFFF' }};
        }

        /* Styles de la marque et du Header */
        .brand-section {
            margin-bottom: 30px;
            width: 100%;
        }
        
        .brand-name {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 4px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            color: {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            text-align: center;
        }
        
        .brand-tagline {
            font-size: 14px;
            font-style: italic;
            color: {{ $qrCode->poster_text_color ?? '#1a1a1a' }};
            opacity: 0.7;
            margin: 0;
            letter-spacing: 1px;
            text-align: center;
        }

        /* Message Principal */
        .message-section {
            margin: 30px 0;
            padding: 0 20px;
            width: 100%;
        }
        
        .main-message {
            font-size: 26px;
            font-weight: 500;
            line-height: 1.4;
            margin: 0;
            text-align: center;
        }

        /* Zone centrale du QR Code */
        .qr-section {
            margin: 30px auto;
            padding: 25px;
            background: #ffffff;
            display: inline-block;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .qr-image {
            display: block;
            margin: 0 auto;
            width: 280px;
            height: 280px;
        }

        /* Informations supplémentaires */
        .info-section {
            margin: 20px 0;
            padding: 0 20px;
            width: 100%;
        }
        
        .info-text {
            font-size: 13px;
            color: {{ $qrCode->poster_text_color ?? '#1a1a1a' }};
            opacity: 0.6;
            line-height: 1.5;
            text-align: center;
        }

        /* Pied de page */
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.1);
            width: 100%;
        }
        
        .scan-instruction {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.6;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .contact-info {
            font-size: 10px;
            opacity: 0.5;
            margin-top: 8px;
            text-align: center;
        }

        /* ==========================================================================
           VARIATIONS DE STYLES PAR TEMPLATE
           ========================================================================== */

        /* 1. Template LUXURY (Style Épuré Or / Sombre par défaut) */
        @if($qrCode->poster_template === 'luxury')
        .poster-container {
            border: 15px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            background: {{ $qrCode->poster_background_color ?? '#FFFFFF' }};
        }
        .brand-name {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 48px;
            letter-spacing: 5px;
        }
        .brand-tagline {
            font-family: 'Georgia', serif;
            font-style: normal;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 4px;
        }
        .main-message {
            font-family: 'Georgia', serif;
            font-size: 22px;
            font-style: italic;
            font-weight: normal;
        }
        .qr-section {
            border: 2px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            box-shadow: none;
        }
        .scan-instruction {
            color: {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            font-weight: bold;
        }

        /* 2. Template ELEGANT (Style Sophistiqué) */
        @elseif($qrCode->poster_template === 'elegant')
        .poster-container {
            border: 1px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            background: linear-gradient(135deg, {{ $qrCode->poster_background_color ?? '#FFFFFF' }} 0%, rgba(212,175,55,0.05) 100%);
        }
        .brand-name {
            font-family: 'Times New Roman', Times, serif;
            font-style: italic;
            text-transform: none;
            font-size: 44px;
        }
        .main-message {
            font-family: 'Times New Roman', Times, serif;
            border-top: 1px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            border-bottom: 1px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            padding: 15px 0;
            display: inline-block;
        }
        .qr-section {
            border-radius: 15px;
        }

        /* 3. Template MODERN (Géométrique et Impactant) */
        @elseif($qrCode->poster_template === 'modern')
        .brand-name {
            font-weight: 800;
            background: {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            color: {{ $qrCode->poster_background_color ?? '#FFFFFF' }};
            display: inline-block;
            padding: 12px 35px;
            border-radius: 50px;
            font-size: 34px;
            letter-spacing: 2px;
        }
        .main-message {
            text-transform: uppercase;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: 2px;
        }
        .qr-section {
            border-radius: 30px;
            background: linear-gradient(135deg, #fff 0%, #f8f8f8 100%);
        }
        .scan-instruction {
            font-weight: bold;
        }

        /* 4. Template MINIMAL (Discret, focus total sur le QR) */
        @elseif($qrCode->poster_template === 'minimal')
        .brand-name {
            font-size: 24px;
            font-weight: 300;
            letter-spacing: 6px;
        }
        .brand-tagline {
            font-size: 11px;
            letter-spacing: 3px;
        }
        .main-message {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.7;
        }
        .qr-section {
            box-shadow: none;
            border: 1px solid #e5e5e5;
            padding: 20px;
        }
        .qr-image {
            width: 220px;
            height: 220px;
        }
        .footer-section {
            border-top: none;
        }

        /* 5. Template CLASSIC (Rendu standard équilibré) */
        @else
        .poster-container {
            border-top: 10px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
            border-bottom: 10px solid {{ $qrCode->poster_primary_color ?? '#D4AF37' }};
        }
        .brand-name {
            font-size: 40px;
        }
        .main-message {
            font-size: 28px;
            font-weight: 500;
        }
        @endif

        /* Impression */
        @media print {
            body {
                background-color: {{ $qrCode->poster_background_color ?? '#FFFFFF' }};
                padding: 0;
                margin: 0;
            }
            .poster-container {
                margin: 0 auto;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .qr-section {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .poster-container {
                padding: 30px 20px;
                width: 95%;
            }
            .brand-name {
                font-size: 28px;
            }
            .main-message {
                font-size: 18px;
            }
            .qr-image {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>

    <div class="poster-container">
        
        <!-- Section Header / Brand -->
        <div class="brand-section">
            @if($qrCode->show_brand_name && isset($company->name) && $company->name)
                <h1 class="brand-name">{{ $company->name }}</h1>
            @endif
            
            @if($qrCode->show_tagline && isset($company->slogan) && $company->slogan)
                <p class="brand-tagline">{{ $company->slogan }}</p>
            @endif
        </div>

        <!-- Section Message Principal -->
        <div class="message-section">
            <h2 class="main-message">
                @if(!empty($qrCode->custom_message))
                    {{ $qrCode->custom_message }}
                @else
                    @if($qrCode->type === 'product')
                        - DÉCOUVREZ CE PRODUIT D'EXCEPTION - 
                    @elseif($qrCode->type === 'category')
                         PARCOUREZ NOTRE COLLECTION 
                    @else
                         SCANNEZ POUR MAGASINER 
                    @endif
                @endif
            </h2>
        </div>

        <!-- Section QR Code -->
        <div class="qr-section">
            <img src="{{ $qrCodeDataUrl }}" alt="QR Code à scanner" class="qr-image">
        </div>

        <!-- Section Informations Complémentaires -->
        @if($qrCode->description)
        <div class="info-section">
            <p class="info-text">{{ $qrCode->description }}</p>
        </div>
        @endif

        <!-- Section Footer / Instructions -->
        <div class="footer-section">
            <p class="scan-instruction">
                 OUVREZ L'APPAREIL PHOTO DE VOTRE SMARTPHONE POUR SCANNER 
            </p>
            @if(isset($company->address) || isset($company->phone))
            <p class="contact-info">
                @if(isset($company->address))
                    {{ $company->address }}
                @endif
                @if(isset($company->address) && isset($company->phone))
                    &nbsp;|&nbsp;
                @endif
                @if(isset($company->phone))
                    {{ $company->phone }}
                @endif
            </p>
            @endif
            <p class="contact-info" style="font-size: 9px; margin-top: 15px;">
                {{ $qrCode->code }}
            </p>
        </div>

    </div>

</body>
</html>