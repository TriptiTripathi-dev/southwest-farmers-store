# Database ownership

This app shares one Postgres database with `warehouse-pos`. The full rules (who creates which tables, how to change a
shared table, the drift check) live in that repo: `warehouse-pos/docs/ARCHITECTURE.md`.

Short version:

- A new shared table gets a guarded migration in `warehouse-pos`, not here.
- Tables created only by this app: `about_page_settings`, `contact_page_settings`, `home_page_settings`,
  `legal_pages`, `newsletter_subscribers`, `quick_pos_settings`, `store_time_logs`.
- `enquiries`, `menu_categories`, `menu_items`, `store_sessions` are created by both apps; the migrations here are
  guarded with `Schema::hasTable` so they are no-ops when the table already exists.
- After changing a model that also exists in `warehouse-pos`, update both copies and run `php artisan schema:drift`
  in both repos (non-zero exit on any model/table mismatch).
- Never move or rename a model class: polymorphic tables such as `store_model_has_roles` store the class name.
