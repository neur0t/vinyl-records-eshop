# Vinyl Records eShop

Απλό δυναμικό eShop για δίσκους βινυλίου.
Υλοποιήθηκε σε PHP + MySQL — απλό και λειτουργικό για εκπαιδευτική χρήση.

**Περιεχόμενο repository**
- Σελίδες: `index.php`, `shop.php`, `cart.php`, `checkout.php`, `login.php`, `register.php`
- Βοηθητικά: `functions.php`, `config.php`, `db_init.php`, `database.sql`
- Assets: `assets/css`, `assets/images`

## Απαιτήσεις
- PHP 7.4+ με MySQLi
- MySQL / MariaDB
- (Προαιρετικό) Composer για δημιουργία PDF (`dompdf/dompdf`)

## Εγκατάσταση τοπικά (XAMPP)
1. Αντιγράψτε το φάκελο στο `htdocs` του XAMPP.
2. Δημιουργήστε τη βάση δεδομένων και τους πίνακες εκτελώντας το `db_init.php` από browser: http://localhost/vinyl-records-eshop/db_init.php
	- Σημείωση: το `db_init.php` χρησιμοποιεί τα credentials σε αυτό το αρχείο. Προσαρμόστε αν χρειάζεται.
3. Ελέγξτε τα στοιχεία σύνδεσης στο `config.php`.

## Χρήση
- Επισκεφθείτε `index.php` για την αρχική σελίδα.
- Πλοηγηθείτε στο `shop.php` για τα προϊόντα (μπορείτε να φιλτράρετε κατά κατηγορία με `?category=ID`).
- Πρέπει να εγγραφείτε/συνδεθείτε για να προσθέσετε προϊόντα στο καλάθι και να ολοκληρώσετε την αγορά.

## Δημιουργία PDF με τον πηγαίο κώδικα
Περιλαμβάνεται ένα script `generate_code_pdf.php` που χρησιμοποιεί `dompdf`. Για να το τρέξετε:

```bash
composer install
# Ανοίξτε στο browser:
http://localhost/vinyl-records-eshop/generate_code_pdf.php
```

Το script θα δημιουργήσει και θα κατεβάσει ένα PDF με τα αρχεία .php και .sql του project.

## Σύνδρομο δοκιμής
- Admin user (εισάγεται στο `database.sql`) — προσαρμόστε το hash του κωδικού στο αρχείο αν θέλετε.

## Σημειώσεις
- Η αποθήκευση πληρωμών είναι εικονική — μην χρησιμοποιείτε πραγματικά στοιχεία κάρτας.
- Για την παραγωγή του PDF απαιτείται composer + dompdf.

Καλή επιτυχία — αν θέλετε, φροντίζω να τρέξω/δοκιμάσω τοπικά ή να δημιουργήσω τα dummy images.
