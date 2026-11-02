# Kaptai Lake — Office Tour Expense Tracker

এক পেজের lightweight Laravel 12 অ্যাপ। Blade + plain CSS/JavaScript; Node, npm, frontend build, CDN, admin panel বা login লাগে না।

## কী আছে

- সদস্যের নাম ও deposited amount যোগ (edit/delete route নেই)
- খরচের category যোগ; duplicate category validation
- Category select করে expense title, amount ও date দিয়ে entry
- মোট জমা, মোট খরচ, remaining balance অথবা shortfall
- Category অনুযায়ী total; member/category/expense instant search
- Category filter ও filtered expense sum; dashboard-এ সব entry-র total
- Responsive tables, native accessible modal dialogs, validation errors-এ modal পুনরায় খোলে
- CSRF, escaped output, foreign key, integer paisa, duplicate submission protection
- সব record add-only; নতুন member entry-তে initial deposit থাকে (আলাদা top-up নয়)

## Requirements

PHP 8.2+ (64-bit), Composer 2; Laravel-required PHP extensions ও SQLite/PDO SQLite। MySQL-ও configure করা যায়।

## প্রথমবার চালানো

ZIP extract করে `kaptai-tour` directory-তে terminal খুলুন:

```bash
composer install
```

`.env.example` ফাইল copy করে `.env` নাম দিন। এরপর:

```bash
php artisan key:generate
php artisan migrate
php artisan serve
```

Browser: http://127.0.0.1:8000

`database/database.sqlite` খালি ফাইল দেওয়া আছে। default configuration নিজেই তার absolute path নেয়; DB_DATABASE দিতে হবে না। প্রথমে Add category, তারপর সদস্য ও খরচ যোগ করুন।

## Hosting

Web document root অবশ্যই `public/` directory। `storage/` ও `bootstrap/cache/` PHP process-এর writable হতে হবে। `.env`-এ APP_ENV=production, APP_DEBUG=false এবং সঠিক APP_URL দিন। তারপর `php artisan optimize` চালাতে পারেন। Laravel vendor dependencies ZIP-এ নেই; `composer install` প্রয়োজন।

এই সংস্করণে authentication নেই: যে এই অ্যাপ খুলতে পারবে সে entry দিতে পারবে। অফিসের private network/host access control দিয়ে ব্যবহার করুন; shared public use চাইলে authentication যোগ করুন।

MySQL চাইলে `.env`-এ DB_CONNECTION=mysql এবং DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD দিন; তারপর migrate করুন। SQLite ও MySQL-এ আলাদা dataset হবে।

## Backup

SQLite ব্যবহার করলে অ্যাপ বন্ধ/maintenance অবস্থায় `database/database.sqlite` কপি রাখুন। `.env`-ও নিরাপদে রাখুন।

## Validation / verification

এই deliverable তৈরির environment-এ PHP ও Composer পাওয়া যায়নি। তাই `composer install`, migration ও Laravel HTTP runtime পরীক্ষা করা হয়নি। JavaScript syntax check করা হয়েছে। Install-এর পরে নিচের acceptance checks করুন:

1. Member 100.10 এবং member 200.20 → total deposit 300.30.
2. Food category-তে expense 25.15 → remaining 275.15.
3. Category add না করে expense button disabled থাকে। Invalid/negative/zero amount server reject করে।
4. Category filter/search করলে filtered sum বদলায়, dashboard total একই থাকে।
5. একই submission_id দিয়ে member/expense request repeat করলে একটি row-ই থাকে।
6. Invalid entry দিলে সংশ্লিষ্ট modal-এ error ও পূর্বের data দেখা যায়।
7. Members, categories ও expenses-এর PUT/PATCH/DELETE route নেই।

কোনো demo/fake financial data preloaded নেই।
