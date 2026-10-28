# atelierhof-kreuzberg.com

Drupal 11 site for Atelierhof Kreuzberg. Until October 2026 it ran as the `ak`
site of the ibase multisite (github.com/d0t15t/ibase-drupal).

## Local setup (ddev)

```bash
ddev start
ddev composer install
ddev import-db --file=path/to/ak-dump.sql.gz
rsync -a path/to/ak-files/ web/sites/default/files/
ddev drush cr
```

The site runs at https://atelierhof-kreuzberg.ddev.site. If `*.ddev.site`
doesn't resolve on your network, add a hosts entry:
`sudo ddev hostname atelierhof-kreuzberg.ddev.site 127.0.0.1`.

## Layout

- Config sync: `config/sync`
- Private files: `private/`, temp files: `tmp/` (both git-ignored)
- Custom code: `web/modules/custom` (ak_theme, ibase_seo,
  ibt_entity_label_override) and `web/themes/custom/ak_b5subtheme`
- Composer patches: `patches/`
- `.ddev/nginx_full/nginx-site.conf` lets Drupal (ibase_seo) serve robots.txt.

## Notes from the multisite migration

- Public files moved from `sites/ak/files` to `sites/default/files`. Stored
  content had no hard-coded `sites/ak/files` links, but old image URLs may be
  indexed, so production should redirect `/sites/ak/files/*` to
  `/sites/default/files/*`.
- `hash_salt` is still `ak`, as on the multisite, so existing sessions and
  image style tokens keep working. Rotate it at a convenient moment.
