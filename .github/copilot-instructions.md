<!-- .github/copilot-instructions.md - Guidance for AI coding agents -->
# Quick orientation — Network Toolbox (PHP)

This repo is a small PHP-based web collection of single-file network diagnostic tools (a lightweight "Network Toolbox").

Summary (big picture)
- PHP front-end: each tool is a standalone PHP page (root or subfolder). Common layout + auth live in `header.php` / `footer.php`.
- `index.php` holds the `$tools` array used to render the dashboard cards — add a new tool here to show it on the home page.
- Tools execute system binaries (nmap, arp-scan, ip, iperf3, tcpdump, whois, dig/dnsutils, speedtest) via `shell_exec`/`passthru` and capture output to `/tmp/netlab_result_{session_id}.txt`.

Key files to examine
- `header.php` — session start, authentication check (redirects to `login.php` unless `$_SESSION['authenticated'] === true`), Bootstrap + navbar. Use this to find nav items and global includes.
- `index.php` — dashboard and `$tools` array. Example tool entries: `['name'=>'ARP Scan','file'=>'arpscan.php',... ]`.
- `login.php` / `logout.php` — simple session-based auth. `header.php` enforces it.
- `export.php` — exports `/tmp/netlab_result_*.txt` to TXT/CSV/PDF (see existing export links in tools).
- Example tools that show patterns: `arpscan.php` (sanitizes interface, calls `/usr/sbin/arp-scan`), `nmap/index.php` (builds nmap command using `escapeshellarg()` and `passthru()`), `iperf.php`, `packet-capture.php`.

Conventions & patterns (important to follow)
- Page scaffold: always `session_start(); include 'header.php'; ... include 'footer.php';` (subdirs use `include '../header.php'`).
- Input/output sanitization:
	- Use `htmlspecialchars()` for HTML output.
	- Use `escapeshellarg()` for command arguments.
	- For interface names the code uses `preg_replace('/[^a-zA-Z0-9.-@]/','', $value)` — follow the same whitelist approach when appropriate.
- Command execution and results:
	- Tools commonly use `ob_start(); passthru($command . ' 2>&1', $return_code); $raw_output = ob_get_clean();` then write to `/tmp/netlab_result_'.session_id().'.txt` and show export links.
	- Check `$return_code` to determine success and present user-friendly messages.
- UI: Bootstrap is loaded from CDN in `header.php`; change layout there.

Developer workflows (practical commands & environment)
- No build system. The app runs on a PHP-enabled webserver (Apache/Nginx+PHP-FPM) or PHP's built-in server for quick testing.
- Required system binaries (examples used in code):
	- `/sbin/ip` (interface discovery)
	- `/usr/sbin/arp-scan`
	- `/usr/bin/nmap`
	- `iperf3`, `tcpdump`, `whois`, `dig`/`nslookup`, `speedtest-cli` (if used)
- Quick dev run (Linux/WSL):
```
cd /path/to/netlab
php -S 0.0.0.0:8000 -t .
# open http://localhost:8000/index.php
```
- Note about `sudo` and permissions: many pages call binaries with `sudo`. For realistic testing you will need the binaries installed and either run the PHP server as a user allowed to execute them or add narrowly scoped sudoers entries (recommended) rather than loosening system permissions.

Security notes (practical)
- The code makes efforts to sanitize inputs, but constructing shell commands from user-provided values is a recurring pattern — always prefer `escapeshellarg()` and whitelists for flags/options.
- Authentication is a simple session flag: `$_SESSION['authenticated']`. Verify `login.php` if changing auth flows.
- Temp results are stored in `/tmp/netlab_result_{session_id}.txt` — consider retention and permissions when deploying.

How to add a new tool (short checklist)
1. Create `mytool.php` in repo root (or subfolder). Start with `session_start(); include 'header.php';` and end with `include 'footer.php';`.
2. Sanitize inputs; use `escapeshellarg()` for any command arguments.
3. Use `ob_start()/passthru()` to capture output and write to `/tmp/netlab_result_'.session_id().'.txt`.
4. Add an entry to `$tools` in `index.php` to show it on the dashboard.
5. Optional: add export links `export.php?format=txt&tool=mytool`.

Examples from the codebase
- `arpscan.php` sanitizes interface names with `preg_replace('/[^a-zA-Z0-9.-@]/', '', $submitted_interface)` and runs `sudo /usr/sbin/arp-scan --interface=... --localnet`.
- `nmap/index.php` uses `escapeshellarg($_POST['target'])`, builds a restricted set of scan-types, and runs `/usr/bin/nmap` via `passthru()`.

If something is unclear or you want the instructions tuned for a particular deployment (PHP version, target OS, or sudo policy), tell me which environment and I will update this file accordingly.