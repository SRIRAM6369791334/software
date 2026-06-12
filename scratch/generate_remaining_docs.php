<?php
$baseDir = realpath(__DIR__ . '/../');
$docsDir = 'C:/Users/srira/.gemini/antigravity/brain/ca2035a5-3548-4eda-a804-abae5e691d45/C4-Documentation';

if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

// 1. Generate API & Endpoints Report
$apiDocsFile = $docsDir . '/api_endpoints.md';
$routeOutput = shell_exec('cd "' . $baseDir . '" && php artisan route:list --json');
$routes = json_decode($routeOutput, true);

$apiMd = "# API & Endpoints Specification Report\n\n";
$apiMd .= "This document lists all the routes and endpoints available in the application.\n\n";
$apiMd .= "| Method | URI | Name | Action | Middleware |\n";
$apiMd .= "|--------|-----|------|--------|------------|\n";

if ($routes) {
    foreach ($routes as $route) {
        $method = $route['method'] ?? 'GET';
        $uri = $route['uri'] ?? '';
        $name = $route['name'] ?? '';
        $action = $route['action'] ?? '';
        $middleware = isset($route['middleware']) ? implode(', ', $route['middleware']) : '';
        
        $apiMd .= "| `{$method}` | `{$uri}` | `{$name}` | `{$action}` | `{$middleware}` |\n";
    }
} else {
    $apiMd .= "| N/A | N/A | N/A | Application not fully bootstrapped for route listing | N/A |\n";
}

file_put_contents($apiDocsFile, $apiMd);
echo "Generated $apiDocsFile\n";

// 2. Generate C4 Component Architecture Report
$compDocsFile = $docsDir . '/c4-component.md';
$compMd = "# C4 Component Architecture\n\n";
$compMd .= "This document breaks down the application into high-level business modules (components).\n\n";

$compMd .= "## Component Diagram\n\n";
$compMd .= "```mermaid\n";
$compMd .= "C4Component\n";
$compMd .= "    title Component diagram for Flockwise Biztrack\n\n";
$compMd .= "    Container_Boundary(api, \"Web Application\") {\n";
$compMd .= "        Component(auth, \"Auth Module\", \"Controllers/Auth\", \"Handles user login, logout, and sessions.\")\n";
$compMd .= "        Component(admin, \"Admin Module\", \"Controllers/Admin\", \"User and Roles/Permissions Management.\")\n";
$compMd .= "        Component(billing, \"Billing Module\", \"Controllers/Billing\", \"Daily & Weekly Bill Generation.\")\n";
$compMd .= "        Component(inventory, \"Inventory Module\", \"Controllers/Inventory\", \"Tracks warehouse stock, items, and bird batches.\")\n";
$compMd .= "        Component(masters, \"Masters Module\", \"Controllers/Masters\", \"Manages Customers, Dealers, Vendors.\")\n";
$compMd .= "        Component(payments, \"Payments Module\", \"Controllers/Payments\", \"Records transactions and ledgers.\")\n";
$compMd .= "        Component(purchases, \"Purchases Module\", \"Controllers/Purchases\", \"Handles purchase invoices.\")\n";
$compMd .= "        Component(reports, \"Reports & Analytics\", \"Controllers\", \"Generates profit, loss, and sales analytics.\")\n";
$compMd .= "    }\n\n";
$compMd .= "    Rel(auth, admin, \"Uses\")\n";
$compMd .= "    Rel(billing, inventory, \"Reads stock from\")\n";
$compMd .= "    Rel(billing, masters, \"Reads customer data from\")\n";
$compMd .= "    Rel(payments, masters, \"Updates ledger for\")\n";
$compMd .= "    Rel(purchases, inventory, \"Updates stock in\")\n";
$compMd .= "    Rel(purchases, masters, \"Reads vendor data from\")\n";
$compMd .= "    Rel(reports, billing, \"Analyzes\")\n";
$compMd .= "    Rel(reports, payments, \"Analyzes\")\n";
$compMd .= "```\n\n";

$compMd .= "## Components List\n\n";
$compMd .= "- **Auth Module**: Responsible for user authentication.\n";
$compMd .= "- **Admin Module**: Manages users, roles, and permissions (Spatie integration).\n";
$compMd .= "- **Billing Module**: Handles Daily and Weekly sales billing with PDF generation and WhatsApp sending.\n";
$compMd .= "- **Inventory Module**: Manages physical stock, warehouses, and bird mortality tracking.\n";
$compMd .= "- **Masters Module**: The core data entities for Customers, Dealers, and Vendors.\n";
$compMd .= "- **Payments Module**: Keeps track of what is owed and what has been paid.\n";
$compMd .= "- **Purchases Module**: Invoice entry for buying stock from Vendors.\n";
$compMd .= "- **Reports & Analytics**: Generates high-level overviews and CSV/PDF exports of profitability.\n";

file_put_contents($compDocsFile, $compMd);
echo "Generated $compDocsFile\n";

// 3. Generate Frontend UI/UX Audit Report
$uiDocsFile = $docsDir . '/frontend_ui_audit.md';
$viewsDir = $baseDir . '/resources/views';

$bladeFiles = 0;
if (is_dir($viewsDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
    foreach ($iterator as $file) {
        if ($file->isFile() && strpos($file->getFilename(), '.blade.php') !== false) {
            $bladeFiles++;
        }
    }
}

$tailwindConfig = file_exists($baseDir . '/tailwind.config.js') ? "Yes" : "No";
$viteConfig = file_exists($baseDir . '/vite.config.js') ? "Yes" : "No";

$uiMd = "# Frontend & UI/UX Audit Report\n\n";
$uiMd .= "## Architecture Overview\n";
$uiMd .= "- **Frontend Stack**: Laravel Blade, Tailwind CSS, Vite\n";
$uiMd .= "- **Total Blade Views**: $bladeFiles\n";
$uiMd .= "- **Tailwind CSS Configured**: $tailwindConfig\n";
$uiMd .= "- **Vite Configured**: $viteConfig\n\n";

$uiMd .= "## UI/UX Assessment\n\n";
$uiMd .= "### 1. Modern Tooling\n";
$uiMd .= "The project uses Vite for asset bundling and Tailwind CSS for styling. This is the modern standard for Laravel applications, offering fast compilation and highly customizable utility-first CSS.\n\n";

$uiMd .= "### 2. Component Reusability (Blade)\n";
$uiMd .= "With $bladeFiles Blade views, the application is likely breaking down UI into manageable components (e.g., layouts, components, partials). Proper use of `<x-component>` tags is recommended for repeated UI elements like buttons, inputs, and modals.\n\n";

$uiMd .= "### 3. Responsive Design\n";
$uiMd .= "Tailwind CSS enforces a mobile-first design approach. Provided the utility classes use breakpoints correctly (`md:`, `lg:`), the application should be fully responsive.\n\n";

$uiMd .= "### 4. Accessibility (A11y)\n";
$uiMd .= "It is crucial to ensure that form inputs have proper labels, buttons have descriptive text, and color contrasts meet WCAG standards. Tailwind provides built-in utilities like `sr-only` to assist screen readers.\n\n";

$uiMd .= "## Recommendations\n";
$uiMd .= "- Extract heavily repeated Tailwind class strings into custom components or `@apply` directives in the main CSS file.\n";
$uiMd .= "- Ensure all interactive elements have visual feedback (hover/focus states).\n";
$uiMd .= "- Maintain a centralized layout file (`app.blade.php`) to avoid duplicating `<head>` and script tags.\n";

file_put_contents($uiDocsFile, $uiMd);
echo "Generated $uiDocsFile\n";
