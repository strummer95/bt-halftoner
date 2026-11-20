# BT Halftoner

Boomer T's DTF halftoner. Turns the partly transparent parts of a design into printed dots, screen
print style. Shortcode `[bt_halftoner]`; BT Portal shows it under **Other > Halftoner** whenever the
shortcode exists (BT Portal 0.60.0+, same pattern as the Quote tab and BT Quote).

- Site: boomerts.com (AWS Lightsail Bitnami WordPress, NOT IONOS; Boomer T's is a separate company
  from Duck and Rabbit Co.)
- Current version: **0.1.1**. Constant `BTH_VERSION`, prefix `bth_`, element ids `bth-`.
- Repo: `strummer95/bt-halftoner` (must stay **public**: the updater reads `manifest.json` through
  `api.github.com` without a token).

## Release process

Same as every BT plugin. Dillon works only through the WordPress dashboard. Four places must match
or WordPress loops on an update it already has:

1. `Version:` in the `bt-halftoner/bt-halftoner.php` header
2. `BTH_VERSION` in the same file
3. `version` in `manifest.json`
4. The version inside the zip

Steps: edit under `bt-halftoner/`, bump both, `php -l` touched files, build `bt-halftoner-X.Y.Z.zip`
and plain `bt-halftoner.zip` at the repo root (zip the folder, not its contents, exclude `.DS_Store`),
update `manifest.json` (`version`, versioned raw `download_url`, prepend a plain-language changelog
entry for shop staff), commit, push to `main`. Dillon: **BT Halftoner > Check for updates**, then
**Plugins > Update Now**.

`includes/bt-admin.php` is byte-identical across the BT plugins (`bt_admin_updates_panel()`, guarded
by `function_exists`). Do not fork it. The updates panel is the last thing on the plugin's own
top-level admin page; never a separate Updates submenu.

## How it works

**Browser only:** no REST, no table, no upload, nothing stored. The engine runs in a Web Worker built
from `htEngine.toString()`. Access: the portal login when BT Portal is active, else a signed-in user
with `edit_posts` (`bth_user_can_use()`).

- **Why it exists.** DTF puts white underbase under any inked pixel, so 50% red prints as pink, not
  as red blended into the shirt. Commercial halftone tools keep the design solid and punch holes in
  it. Dillon wants the opposite, screen print style: solid stays solid, the fade becomes dots of
  full-strength ink that shrink to nothing.
- **Output rule, do not break it:** every pixel is alpha 0 or 255. Dot colour is the pixel's own
  un-premultiplied RGB (decoded with `premultiplyAlpha:'none'`), so a faded red is dots of real red.
- Dot area is linear in opacity: `radiusFor()` inverts the coverage of a round dot clipped by its
  cell (full cover only at r = sqrt(0.5) of the cell). Alpha is averaged over each cell; at or above
  "Solid above" (default 97) a pixel prints solid. **Below "Smallest dot kept" dots stop shrinking and
  thin out instead**: a min-size dot in a fraction a/minDot of cells (repeatable per-cell hash). The
  first build dropped those cells outright and Dillon saw a hard cut-off ring; do not go back to that.
- **Preview downscaling matters.** Shrinking the dot pattern in one `drawImage` step skips pixels and
  made the dots look harsh with a solid core. `paint()` halves repeatedly so each step averages. Left
  preview modes: As designed (default, the goal) and As DTF prints it today (fade toward white).
- Output defaults to 2x pixels (300 DPI in, 600 DPI out, same print size) because at 300 DPI / 45 LPI a
  cell is 6.7px and dots come out as blocky crosses. The pHYs chunk is written so RIPs size it right.
- BT Portal's `btSwitchTab()` calls `window.bthShow()` to redraw, since a canvas fitted while its pane
  was hidden is 0 wide. Keep that name; the portal depends on it.
- **Magnifier (0.1.1).** At Fit the averaged preview makes a halftone look like the original, and
  Dillon reported "it didn't do anything" on a gradient. Hovering either stage shows a loupe on both
  sides at the same print spot: halftoned at 1 file px per screen px, original covering the same area.
  `paint()` keeps the full-res composite on `canvas._full` for it. Stage background is a checkerboard
  so empty space is not read as white ink.
- **Illustrator gradients to 0% opacity** also interpolate colour toward the 0% stop's colour (white
  by default), so the faint end is light grey ink. That is in the file, not a bug here; the fix is
  the stop colour in Illustrator.
- Only transparency is dotted. An image with no alpha (JPG) is left solid and the status line says so.

## Working notes

Compact UI, text 13px or larger. Brand navy `#27267e`, pink `#e535ab`, Oswald. Terse, results-first.
