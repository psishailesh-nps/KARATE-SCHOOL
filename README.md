# JETPUR KARATE TEAM ERP

Karate Academy ERP inspired by the workflow of modern school ERP systems.

## Current architecture

- PHP 8.x + MySQL backend
- PDO prepared statements
- Secure PHP session authentication for admin accounts
- Central ERP data store for the existing modules
- Audit log table
- Local browser cache for fast UI
- Role-based admin navigation
- Student/Parent and Coach portal pages
- Karate-specific belts, grading, tournaments and certificates

## Main modules

Dashboard, Admission / Front Office, Students, Coaches, Batches, Attendance, Fees, Expenses / Accounts, Coach Payroll, Belt Promotion, Exams, Tournaments, Certificates, Notices / Events, Reports and Settings.

## Database setup

1. Create a MySQL database.
2. Import `database/schema.sql`.
3. Edit `api/config.php`:
   - DB_HOST
   - DB_NAME
   - DB_USER
   - DB_PASS
4. Upload the complete repository to PHP hosting.
5. Use HTTPS.
6. Open `index.html`.

Default admin:
- Username: `admin`
- Password: `admin123`
- Role: `Super Admin`

Change the default password before real use.

## Important

GitHub Pages cannot run the PHP/MySQL backend. The upgraded version must be hosted on PHP + MySQL hosting. The frontend can still be kept in GitHub, but the production URL should point to the PHP hosting.

The central store synchronizes the existing module data into MySQL so the current UI can move from browser-only storage to shared server persistence without rebuilding every screen from scratch.

For production, use HTTPS and keep database credentials private. PHP's session security guidance recommends strict mode, cookie-only sessions, HttpOnly cookies and SameSite protection; this project enables those settings.