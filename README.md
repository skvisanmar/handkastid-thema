# Handkastið – nýtt þema

- `theme/handkastid/` – WordPress-þemað sjálft (þetta fer á lifandi síðuna sem zip-skrá).
- `local/` – prufugögn (65 fréttir af handkastid.net) og uppsetning fyrir staðbundna prufusíðu.
- `forsida-drog.html` – upphaflegu drögin.

## Keyra á eigin vél

```sh
./start.sh
```

Opna svo http://127.0.0.1:9400 – innskráning: `admin` / `password` (http://127.0.0.1:9400/wp-admin).

Þarf aðeins Node.js. Prufusíðan byrjar alltaf upp á nýtt við ræsingu, svo breytingar í stjórnborðinu
geymast ekki, en breytingar á skránum í `theme/handkastid/` sjást strax við endurhleðslu.

## Fyrir stjórnendur

- **Aðalfrétt:** nýjasta frétt sem er merkt „Festa efst“ (Sticky) í ritlinum, annars nýjasta fréttin.
- **Mest lesið:** fréttir í flokknum „Mest lesið“, annars flettingar úr WP-PostViews.
- **Auglýsingar:** búa til staðsetningar (placements) í Advanced Ads með auðkennunum
  `hk-top`, `hk-side`, `hk-feed-1`, `hk-feed-2`, `hk-sticky`, `hk-bill` og `hk-article`.
- **Kostunaraðilar, hlaðvarpsslóðir og samfélagsmiðlar:** Útlit → Sérsníða → Handkastið.
  Þar er líka slökkt á merktu auðu auglýsingaplássunum áður en síðan fer í loftið.

## Ekki enn tengt

Leikjavaktin, stöðutöflur og næstu leikir sýna sýnidæmi. Raunveruleg gögn tengjast í
`inc/games.php` með síunum `hk_games`, `hk_standings` og `hk_upcoming` þegar ljóst er hvaðan þau koma.
