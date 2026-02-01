INSERT IGNORE INTO migrations (version, class, `group`, namespace, time, batch) 
VALUES ('2026-02-01-100001', 'App\\Database\\Migrations\\CreateAdditionalTables', 'default', 'App', NOW(), 1);
