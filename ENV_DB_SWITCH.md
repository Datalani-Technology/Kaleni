# Switching DB: Local Test ↔ Production

**.env** has two DB blocks. Only **one** must be active (uncommented) at a time.

---

## Right now (testing)

- **Active:** **LOCAL TEST (old phpMyAdmin)** → `namsa_flora` @ `127.0.0.1`, user `root`.
- Migrations have been run on this DB.
- Use this for local testing. Export/backup this DB manually if you need a copy.

---

## When you’re done testing (“take it back”)

1. **Export the local test DB** from phpMyAdmin if you want a backup (you said you’ll do this manually).
2. **Edit `.env`**:
   - **Comment out** the **LOCAL TEST** block (all `DB_*` lines for namsa_flora).
   - **Uncomment** the **PRODUCTION / MAIN** block and set:
     - `DB_HOST` (often `localhost` on cPanel)
     - `DB_DATABASE` (your cPanel database name)
     - `DB_USERNAME` (your cPanel MySQL user)
     - `DB_PASSWORD` (your cPanel MySQL password)
3. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

After that, the app uses the production/main DB again. The local test details stay in `.env` as comments so you can switch back later.

---

## Quick reference

| Use case        | Active block in .env     | Comment out        |
|-----------------|--------------------------|--------------------|
| Local testing   | LOCAL TEST (phpMyAdmin)  | PRODUCTION         |
| Production/live | PRODUCTION (cPanel)      | LOCAL TEST         |
