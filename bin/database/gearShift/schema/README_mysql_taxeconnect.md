# TaxeConnect MySQL Bootstrap

## Files
- `bin/database/gearShift/schema/mysql_taxeconnect_schema.sql`
- `bin/database/gearShift/schema/mysql_taxeconnect_seed.sql`

## Execute
```bash
mysql -u <user> -p < bin/database/gearShift/schema/mysql_taxeconnect_schema.sql
mysql -u <user> -p < bin/database/gearShift/schema/mysql_taxeconnect_seed.sql
```

## Important
- Replace placeholder bcrypt hashes before using in production.
- Set a strong password for `superadmin`.
- Keep one `MAIRIE_ADMIN` account per commune.
- Agents must only create merchants in their assigned commune.
- `users.role` supports: `SUPER_ADMIN`, `MAIRIE_ADMIN`, `AGENT`, `MERCHANT`.
- `merchants.user_id` links a merchant account when self-service access is enabled.
