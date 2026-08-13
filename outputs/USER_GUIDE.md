# FLOCKWISE BIZTRACK POULTRY MANAGEMENT SYSTEM - COMPLETE USER GUIDE

Welcome to the **Flockwise Biztrack Poultry Management System User Guide**. This comprehensive document provides step-by-step instructions and functional references for every module, form, action, and report in your admin panel.

To help you navigate easily, all sections in this guide use the exact names and order shown in your left sidebar menu.

---

## Sidebar Navigation Menu Reference

Here is the exact mapping of sidebar names to their core system functions:

- **Dashboard**: Central operational overview, real-time sales/dues metrics, active batch widgets, and urgent system alerts.
- **Dayload Billing**: Dispatch management for live bird shipments, crate/farm weight entry, paper/customer rate calculations, and vendor/dealer margin tracking.

### Master Records
- **Customers**: Manage retail and wholesale customer accounts, address details, routes, balance tracking, and ledger downloads.
- **Dealers**: Commercial dealer registry, GST identification, delivery route assignments, and outstanding balance ledgers.
- **Vendors**: Suppliers for live chicks, feed, medicines, and equipment, purchase history, and payout ledgers.

### Operations
- **Daily Billing Generate**: Create, view, export, and send daily customer invoices and GST calculations via PDF/WhatsApp.
- **Weekly Billing Generate**: Consolidate weekly dealer billing, calculate unbilled dayload previews, and generate bulk invoices.
- **Purchases**: Entry portal for recording raw material, feed, chick, and medicine purchases from vendors.
- **Purchase Invoices**: Search, filter, export, print, and download PDF copies of purchase invoices.

### Finance & Payments
- **Dealer Payments**: Record money collected from dealers (Cash vs Bank), allocate payments via FIFO, and track dealer balance rollups.
- **Vendor Payments**: Record payouts to vendors (Cash vs Bank) and automatically allocate payments to open dayloads.
- **Cash & Bank Ledger**: Track daily opening/closing balances, cash/bank income vs expense, and execute Admin approval sweeps.
- **Expenses**: Record operational business expenses across custom categories (diesel, repairs, feed transport, staff salaries).
- **EMI Records**: Track loans, monthly EMI payment schedules, due alerts, and full loan closures.

### Performance
- **Profit & Loss**: Executive financial dashboard, batch-level profitability, monthly trends, and expense vs. income comparisons.

### Administration
- **User Management**: Create system accounts, set passwords, assign roles, and toggle active/inactive status.
- **Role Management**: Define custom RBAC roles and assign granular permission matrices.
- **Permissions**: Reference system permission keys enforced across routes and actions.

---

## 1. Login and System Access

### 1.1 Accessing the Login Portal
1. Open your web browser and navigate to the application URL (e.g., `https://yourdomain.com/login`).
2. If you are already logged in, the system automatically redirects you to the main **Dashboard**.

### 1.2 Login Form Features & Inputs
The login screen features a glassmorphism card with real-time field validation and interactive controls:

| Form Field | Input Type | Description | Mandatory? |
| :--- | :--- | :--- | :--- |
| **Email or Username** | Text | Enter your registered email address (e.g., `admin@poultry.com`) or username (e.g., `admin`). | Yes |
| **Password** | Password | Enter your account password. Click the **Eye Icon** on the right side of the input to toggle password visibility. | Yes |
| **Sign In Button** | Submit | Submits credentials for authentication. | Yes |

### 1.3 How to Log In Step-by-Step
1. Type your registered **Email or Username** into the first field.
2. Type your **Password** into the password field.
3. Click the **Sign In** button.
4. If your credentials are correct and your account is active, you will be redirected to the **Dashboard**.

### 1.4 Security & Rate Limiting Rules
- **Rate Limit (Throttle)**: To protect against unauthorized brute-force attempts, the system enforces a strict limit of **10 login attempts per minute**. Exceeding this limit will temporarily lock the login form.
- **Active Account Check**: Accounts marked as inactive in User Management cannot log in.
- **Session Security**: Upon successful login, the system automatically regenerates your session ID to prevent session fixation attacks.

### 1.5 Common Login Error Messages
- *"These credentials do not match our records."*: The username/email or password entered is incorrect. Double-check your spelling or contact your administrator.
- *"Your account is deactivated."*: Your account status has been disabled by an administrator in **User Management**.
- *"Too many login attempts. Please try again in X seconds."*: You exceeded 10 failed login attempts in 60 seconds. Wait for the timer to expire before retrying.

### 1.6 How to Log Out
1. Locate the **User Profile Card** at the bottom of the left sidebar.
2. Click the red **Logout (power icon)** button.
3. Your session will be invalidated, security tokens cleared, and you will be returned to the **Login Page**.

---

## 2. Dashboard Overview & Navigation

### 2.1 Summary Metrics & Cards
The **Dashboard** serves as your central command center:
- **Total Day's Sales**: Total monetary value of dayload dispatches and invoices issued today.
- **Active Batches**: Number of live bird batches currently being raised or processed.
- **Outstanding Dues**: Combined unpaid balance owed by customers and dealers.
- **Cash & Bank Balances**: Real-time closing cash balance and bank balance.

### 2.2 System Alerts Widget
The top section of the dashboard displays automated alerts:
- **EMI Due Alerts**: Notifies you of loan EMIs due within 7 to 30 days. Click **View Alerts** to see the full list in `/dashboard/alerts`.
- **Low Inventory Alerts**: Flags items reaching critical minimum stock thresholds.

### 2.3 Topbar Navigation Bar
- **Global Search Bar**: Click to search customers, dealers, vendors, or invoice numbers instantly.
- **Theme Toggle**: Switch between Dark Mode and Light Mode seamlessly.
- **Notification Bell**: Displays live unread notifications. Click **Mark All as Read** to clear alerts.

---

## 3. Global Search and Suggestions

Located at the top header of the admin panel is the **Global Search Bar**.

### How to Search
1. Click on the search bar or press your cursor inside it.
2. Type a minimum of **2 characters** (e.g., customer name, dealer firm, vendor name, or invoice number like `INV-D-0016` or `INV-W-0005`).
3. View instant suggestions:
   - **Customers** (Emerald icon): Direct link to Customer Profile.
   - **Dealers** (Blue icon): Direct link to Dealer Profile.
   - **Vendors** (Amber icon): Direct link to Vendor Ledger.
   - **Daily Invoices** (Violet icon): Direct link to view/print Daily Invoice.
   - **Weekly Invoices** (Purple icon): Direct link to view Weekly Invoice.
4. Click any suggestion to navigate directly to that record.

---

## 4. Dayload Billing

Use this section to manage live bird shipments from vendors to dealers, record box weights, calculate net bird weight, apply paper and customer rates, and track gross profit margins.

### 4.1 Dayload Data Fields
| Field Name | Description | Example Value |
| :--- | :--- | :--- |
| **Billing Date** | Date of shipment | `2026-08-12` |
| **Vendor** | Supplying vendor | `Standard Chick Supplies` |
| **Dealer** | Purchasing dealer | `Metro Poultry Traders` |
| **Paper Rate (₹/kg)** | Base cost rate paid to vendor | `110.00` |
| **Customer Rate (₹/kg)** | Billing rate charged to dealer | `118.00` |
| **No. of Boxes** | Number of crates transported | `50` |
| **Box Weight (kg)** | Total gross weight (birds + boxes) | `1,250.00` |
| **Empty Weight (kg)** | Weight of empty boxes | `150.00` |
| **Farm Weight (kg)** | Optional weight recorded at farm | `1,095.00` |
| **Remarks** | Vehicle number or load details | `TN-38-AB-1234` |

### 4.2 Automated Calculations Formula
- **Net Bird Weight** = `Box Weight` - `Empty Weight` (e.g., 1,250 - 150 = 1,100 kg)
- **Dealer Income** = `Net Bird Weight` × `Customer Rate` (e.g., 1,100 × ₹118 = ₹129,800)
- **Vendor Cost** = `Net Bird Weight` × `Paper Rate` (e.g., 1,100 × ₹110 = ₹121,000)
- **Gross Margin** = `Dealer Income` - `Vendor Cost` (e.g., ₹129,800 - ₹121,000 = ₹8,800 profit)

### 4.3 Transfer & Split Shipments
If a shipment is diverted or divided among multiple dealers:
1. Click **Transfer / Split** next to the entry.
2. Select the new Dealer and enter the transferred weight/boxes.
3. The system sets the original status to `Adjusted`, logs the transfer in **Entry Adjustment Logs**, and generates the child entry.

### 4.4 Payment Status Management
- **Record Dealer Payment**: Input Cash/Bank amounts collected from the dealer.
- **Record Vendor Payment**: Input Cash/Bank amounts paid to the vendor.
- Status automatically updates to `Pending`, `Partial`, or `Paid`.

---

## 5. Master Records

### 5.1 Customers
Manage individual or business buyers purchasing poultry items.

#### Form Fields & Actions
- **Add Customer**: Click **Add Customer** -> enter **Name**, **Phone**, **Address**, **GST Number**, **Route**, and **Customer Type** (`Retail` / `Wholesale`).
- **View Profile**: View purchase totals, top items bought, active EMIs, and daily invoice history.
- **Download Ledger PDF**: Downloads a PDF account statement for balance reconciliation.

---

### 5.2 Dealers
Manage commercial dealers purchasing bulk shipments.

#### Form Fields & Actions
- **Add Dealer**: Click **Add Dealer** -> enter **Firm Name**, **Contact Person**, **Phone**, **GST Number**, **Location**, **Route**, and **Opening Pending Balance**.
- **Outstanding Report**: View a complete breakdown of dayload balances, weekly bills, and payment records.
- **Download Ledger PDF**: Generate a PDF summary of dealer transactions.

---

### 5.3 Vendors
Manage suppliers supplying chicks, feed, medicines, and supplies.

#### Form Fields & Actions
- **Add Vendor**: Click **Add Vendor** -> enter **Firm Name**, **Contact Person**, **Phone**, **GST Number**, **Address**, and **Opening Balance**.
- **Purchase History**: View raw material purchases and dayload dispatches supplied by the vendor.
- **History PDF**: Download a PDF statement of vendor liabilities.

---

## 6. Operations & Billing

### 6.1 Daily Billing Generate
Generate daily tax invoices for customers purchasing products.

1. Click **Daily Billing Generate** -> **Add New Invoice**.
2. Select **Customer**, **Invoice Date**, and select line items (e.g., Broiler Chicken, Eggs, Feed).
3. Specify **Quantity (kg/units)** and **Rate per kg**.
4. The system calculates **Subtotal**, **GST Tax**, **Discount**, and **Net Amount**.
5. Actions available: **Print**, **Download PDF**, **Send via WhatsApp**.

---

### 6.2 Weekly Billing Generate
Consolidate weekly billing for commercial dealers.

1. Click **Weekly Billing Generate** -> **Generate Dealer Weekly Invoice**.
2. Select **Dealer** and date range (**Period Start** to **Period End**).
3. Click **Calculate Preview** to pull all unbilled Dayload entries and daily purchases.
4. Review calculated totals and click **Generate Weekly Bill**.
5. Use **Bulk Weekly Billing** to process all active dealers at once.

---

### 6.3 Purchases & Purchase Invoices
Record raw material and chick purchases.

1. Click **Purchases** -> select **Vendor**, **Date**, **Bill Number**, and **Payment Mode** (`Cash`, `Bank`, `Credit`).
2. Add line items: **Item Name**, **Quantity**, **Unit Price**, **Tax**.
3. View invoice archive in **Purchase Invoices** with options to filter, export CSV, or print PDF.

---

## 7. Finance & Payments

### 7.1 Dealer Payments
Log collections received from dealers.

1. Click **Dealer Payments** -> **Record Dealer Payment**.
2. Select **Dealer**, **Date**, **Payment Mode** (`Cash`, `Bank`, `Split`).
3. Enter **Cash Amount** and/or **Bank Amount** (if bank, select type: `UPI`, `NEFT`, `RTGS`, `IMPS`, `Cheque` and input **Reference Number**).
4. Enter optional **Discount Amount** or **Notes**.
5. Payments automatically credit against unpaid dayload dispatches using FIFO (First-In, First-Out).

---

### 7.2 Vendor Payments
Log payouts sent to suppliers.

1. Click **Vendor Payments** -> **Add Vendor Payment**.
2. Select **Vendor**, **Date**, **Amount**, **Cash/Bank Split**, and **Reference Number**.
3. Unallocated funds automatically settle open vendor dayload liabilities.

---

### 7.3 Cash & Bank Ledger
Track daily cash and bank balances.

- Displays **Opening Cash/Bank Balances**, **Daily Cash/Bank Incomes**, **Daily Cash/Bank Expenses**, and **Closing Balances**.
- **Admin Approval Sweep**: Administrator reviews daily totals and clicks **Approve Ledger** to lock the financial day.

---

### 7.4 Expenses
Record operational business overheads.

1. Click **Expenses** -> enter **Date**, select **Category** (e.g., `Vehicle Maintenance`, `Diesel`), **Description**, **Amount**, and **Payment Method** (`Cash` / `Bank`).
2. Export expense logs anytime via **Export CSV**.

---

### 7.5 EMI Records
Manage loan schedules and financing terms.

1. Click **EMI Records** -> **Add EMI Record**.
2. Select Entity (`Customer`, `Dealer`, `Vendor`, or `Bank Loan`), enter **Loan Title**, **Total Amount**, **Monthly EMI Amount**, **Interest Rate**, and **Due Day**.
3. Actions: **Pay Monthly EMI** or **Close Full Loan**.

---

## 8. Performance & Profit Analytics

Access executive-level profitability reports under **Profit & Loss**:

- **Gross Margin Overview**: Total Sales Revenue minus COGS.
- **Weekly & Monthly Detail**: Comparative financial trends across weeks and months.
- **Batch Profitability**: Detailed breakdown of Feed Consumption, Mortality Loss, Vendor Cost, and Net Margin per bird for any active or closed batch.
- **Exports**: Download report data in PDF or CSV formats.

---

## 9. Administration

### 9.1 User Management
1. Click **User Management** -> **Create New User**.
2. Enter **Full Name**, **Username**, **Email**, **Password**, and select **Role**.
3. Toggle account status using **Activate / Deactivate**.
4. View real-time security audit logs in **Activity Logs**.

---

### 9.2 Role Management
1. Click **Role Management** -> **Create New Role** or **Assign Permissions**.
2. Select permission checkboxes to configure access matrices for users.
3. System Admin role is protected with Anti-Lockout safeguards.

---

## 10. System Maintenance & Troubleshooting

- **Live Deploy Sync**: Visit `yourdomain.com/live-deploy-sync-2026` to run migrations and clear caches on shared hosting servers.
- **Full Database Export**: Visit `yourdomain.com/export-db-2026` to download a complete SQL database dump.
- **Database Restoration**: Visit `yourdomain.com/import-sql-dump` to seed database backups.

---
*End of User Guide.*
