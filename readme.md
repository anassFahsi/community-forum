# Community Forum

Ett community-forum byggt i PHP där användare kan skapa konton, gå med i grupper och diskutera olika ämnen. Projektet är gjort som en skoluppgift och följer kraven för säker databehandling, grupphantering och diskussionstrådar.

## Funktioner

### 👤 Användare
- Registrera konto (förnamn, efternamn, e‑post, lösenord hashat)
- Logga in / logga ut
- Se grupper man är medlem i
- Se grupper man inte är medlem i

### 👥 Grupper
- Skapa ny grupp
- Ansöka om att gå med i grupp
- Medlemmar kan se diskussioner i gruppen
- Medlemmar kan starta diskussioner
- Medlemmar kan svara på diskussioner

### 🔐 Säkerhet
- Alla SQL‑frågor använder prepared statements
- Sessionkontroller på alla sidor
- Användare kan endast se grupper och diskussioner de har rätt till

## Databasstruktur (kort översikt)

- **users** – lagrar användare
- **groups** – lagrar grupper
- **group_members** – kopplar användare till grupper
- **group_join_requests** – ansökningar om medlemskap
- **discussions** – diskussioner inom grupper
- **posts** – inlägg i diskussioner
- **notifications** – notiser för ansökningar och godkännanden
- **invitation_links** - lagrar inbjudnings länkar

## Teknisk stack
- PHP (ingen React enligt uppgiftskrav)
- MySQL / MariaDB
- Tailwind CSS
- Lite JavaScript för UX (hamburgarmeny, toggles)

## Installation
1. Klona projektet
2. Importera SQL‑filen i din databas
3. Uppdatera `includes/db.php` med dina databasuppgifter
4. Kör projektet via lokal server (t.ex. XAMPP, MAMP eller Vercel PHP runtime)

## Inlämningsmaterial
- 3 screenshots:
  - Startsidan
  - Gruppdiskussion
  - Svara på diskussion
- Genomgångsvideo:
  - Skapa konto
  - Ansöka om grupp
  - Godkänna ansökan
  - Skapa diskussion
  - Svara på diskussion

## Syfte
Projektet är gjort som en skoluppgift för att träna:
- PHP‑utveckling
- Databasdesign
- Sessionshantering
- Säker databehandling
- Arkitektur och struktur i kodbas
