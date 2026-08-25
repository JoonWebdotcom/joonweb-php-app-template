<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joonweb PHP App Dashboard</title>
    <script src="https://apps.joonweb.com/app-bridge.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #111827;
            --bg-color: #F9FAFB;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            --text-main: #111827;
            --text-muted: #6B7280;
            --success: #059669;
            --success-bg: #D1FAE5;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 32px 24px;
            color: var(--text-main);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }
        h1 { margin: 0 0 8px 0; font-size: 24px; font-weight: 600; letter-spacing: -0.02em; }
        .subtitle { color: var(--text-muted); font-size: 15px; margin: 0; line-height: 1.5; }
        .error-banner {
            background: #FEF2F2;
            color: #991B1B;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            border: 1px solid #FECACA;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .product-card {
            background: var(--surface);
            border-radius: 8px;
            padding: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .product-icon {
            width: 40px;
            height: 40px;
            background: #F3F4F6;
            color: var(--text-muted);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-icon svg { width: 20px; height: 20px; stroke: currentColor; stroke-width: 1.5; fill: none; }
        .product-info h3 { margin: 0 0 4px 0; font-size: 14px; font-weight: 500; }
        .product-info p { margin: 0; color: var(--text-muted); font-size: 14px; }
        .empty-state { text-align: center; padding: 48px 24px; color: var(--text-muted); font-size: 14px; }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 24px;
        }
        .header-section h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <div data-joonweb-app 
         data-api-key="<?php echo htmlspecialchars($apiKey ?? ''); ?>" 
         data-site="<?php echo htmlspecialchars($site ?? ''); ?>" 
         data-host="<?php echo htmlspecialchars($host ?? ''); ?>">
    </div>

    <div class="container">
        <div class="card">
            <h1>App Dashboard</h1>
            <p class="subtitle">This is a standalone PHP MVC scaffold connected securely to the Joonweb API.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-banner">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="padding: 24px;">
            <div class="header-section">
                <h2>Recent Products</h2>
                <button id="openPickerBtn" class="btn-primary">Select via App Bridge</button>
            </div>
            
            <script>
                document.getElementById('openPickerBtn').addEventListener('click', async function(e) {
                    e.preventDefault();
                    if (window.joonwebApp && window.joonwebApp.actions.Components) {
                        const Components = window.joonwebApp.actions.Components.create(window.joonwebApp);
                        try {
                            const data = await Components.show('ProductPicker');
                            console.log('Product Picker Data:', data);
                        } catch(err) {
                            console.error('Picker error:', err);
                        }
                    }
                });
            </script>
            
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    No products found, or the API returned an empty list.
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $title = is_object($product) ? ($product->title ?? 'Unnamed') : ($product['title'] ?? 'Unnamed');
                            $price = is_object($product) ? ($product->price ?? '0.00') : ($product['price'] ?? '0.00');
                        ?>
                        <div class="product-card">
                            <div class="product-icon">
                                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($title); ?></h3>
                                <p><?php echo htmlspecialchars($price); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
