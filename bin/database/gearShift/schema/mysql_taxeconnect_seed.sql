-- TaxeConnect CI - MySQL seed data (dev bootstrap)
-- Execute after mysql_taxeconnect_schema.sql

-- Replace password hashes with your own bcrypt hashes in production.
-- Example hash below corresponds to a placeholder password.

INSERT INTO users (
  login, password_hash, full_name, email, phone, role, status, commune_id, created_by
) VALUES (
  'superadmin',
  '$2y$10$replace.with.your.bcrypt.hash',
  'Super Administrateur',
  'superadmin@taxeconnect.ci',
  '+225000000000',
  'SUPER_ADMIN',
  'ACTIVE',
  NULL,
  NULL
);

INSERT INTO communes (code, name, department, region)
VALUES ('ABJ-YOP', 'Yopougon', 'Abidjan', 'District Autonome d''Abidjan');

INSERT INTO users (
  login, password_hash, full_name, email, phone, role, status, commune_id, created_by
)
SELECT
  'mairie_yop',
  '$2y$10$replace.with.your.bcrypt.hash',
  'Administrateur Mairie Yopougon',
  'mairie.yop@taxeconnect.ci',
  '+225010000001',
  'MAIRIE_ADMIN',
  'ACTIVE',
  c.id,
  sa.id
FROM communes c
JOIN users sa ON sa.login = 'superadmin'
WHERE c.code = 'ABJ-YOP';

INSERT INTO mairies (commune_id, mairie_admin_user_id, created_by_super_admin)
SELECT
  c.id,
  ma.id,
  sa.id
FROM communes c
JOIN users ma ON ma.login = 'mairie_yop'
JOIN users sa ON sa.login = 'superadmin'
WHERE c.code = 'ABJ-YOP';

INSERT INTO users (
  login, password_hash, full_name, email, phone, role, status, commune_id, created_by
)
SELECT
  'agent_yop_01',
  '$2y$10$replace.with.your.bcrypt.hash',
  'Agent Yopougon 01',
  'agent01.yop@taxeconnect.ci',
  '+225010000101',
  'AGENT',
  'ACTIVE',
  c.id,
  ma.id
FROM communes c
JOIN users ma ON ma.login = 'mairie_yop'
WHERE c.code = 'ABJ-YOP';

INSERT INTO users (
  login, password_hash, full_name, email, phone, role, status, commune_id, created_by
)
SELECT
  'merchant_yop_01',
  '$2y$10$replace.with.your.bcrypt.hash',
  'Commercant Yopougon 01',
  'merchant01.yop@taxeconnect.ci',
  '+225070000001',
  'MERCHANT',
  'ACTIVE',
  c.id,
  ag.id
FROM communes c
JOIN users ag ON ag.login = 'agent_yop_01'
WHERE c.code = 'ABJ-YOP';

INSERT INTO agents (commune_id, agent_user_id, created_by_mairie_admin)
SELECT
  c.id,
  ag.id,
  ma.id
FROM communes c
JOIN users ag ON ag.login = 'agent_yop_01'
JOIN users ma ON ma.login = 'mairie_yop'
WHERE c.code = 'ABJ-YOP';

INSERT INTO merchants (
  merchant_ref, user_id, commune_id, created_by_agent_user_id, business_name, owner_name,
  phone, email, activity, address, status
)
SELECT
  'MRC-YOP-0001',
  mer.id,
  c.id,
  ag.id,
  'Boutique Soleil',
  'Kouassi Koffi',
  '+2250701020304',
  'boutique.soleil@taxeconnect.ci',
  'Commerce general',
  'Yopougon Toits Rouges',
  'ACTIVE'
FROM communes c
JOIN users ag ON ag.login = 'agent_yop_01'
JOIN users mer ON mer.login = 'merchant_yop_01'
WHERE c.code = 'ABJ-YOP';
