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

## Gögn frá HSÍ

Leikjavaktin, stöðutöflur, úrslit og næstu leikir eru sótt beint úr vefþjónustu HSÍ
(`https://www.hsi.is/api/hsi/tournaments/{mót}/matches` og `/standings`) og geymd í 10 mínútur.
Ef HSÍ svarar ekki eru síðustu gögn notuð. Mótanúmerin breytast á hverju tímabili og eru í
`hk_leagues()` í `inc/games.php` (2026–27: Olís karla 9142, Olís kvenna 9141, Grill 66 karla 9140,
Grill 66 kvenna 9143). Þar eru líka svæðin í töflunum (úrslitakeppni, umspil, fall).



