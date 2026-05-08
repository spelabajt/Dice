# 🦋 Butterfly: Digital Dice Experience

**Butterfly Sanctuary** ni le preprosta igra s kockami, temveč spletna aplikacija, ki združuje estetiko "Glassmorphism-a" in tekočo uporabniško izkušnjo. Projekt je zasnovan kot lahkotna družabna igra, kjer igralci (metulji) tekmujejo v zbiranju točk skozi strateško nastavljene mete kock.

---

## 💎 Ključne Lastnosti

### 1. Dinamično upravljanje igralcev
Sistem omogoča dodajanje do 6 igralcev. Vsaka kartica igralca je opremljena z unikatnim identifikatorjem, ki omogoča brisanje posameznika brez vpliva na ostale podatke v seji.

### 2. Prilagodljiva igralna mehanika
Pred začetkom igre se določi usoda metov:
* **Število kock:** Izbira med 1, 2 ali 3 kockami hkrati.
* **Število ponovitev (Reps):** Določa, kolikokrat lahko vsak igralec vrže kocke (1–10 krogov).

### 3. "Glassmorphism" Dizajn
Uporabniški vmesnik uporablja sodobne CSS tehnike:
* `backdrop-filter: blur(20px)` za učinek zamegljenega stekla.
* Neon vijolične in modre sence (`box-shadow`) za globino.
* Odziven (Responsive) Flexbox mrežni sistem.

---

## 🛠 Tehnični vpogled

### Upravljanje seje (Session Handling)
Igra močno sloni na PHP sejah (`$_SESSION`). Logika resetiranja je implementirana na dveh ravneh:
1. **Popoln Reset:** Ob vstopu preko `index.html` se uporabi `session_destroy()`, kar zagotovi, da stara igra ne vpliva na nove igralce.
2. **Mehak Reset:** Gumb "Play Again" na koncu igre počisti le polje igralcev, ohrani pa sistemske nastavitve, če je to potrebno.

### JavaScript Metanje (Dice Logic)
Namesto nenehnega osveževanja strani, `s2.php` uporablja asinhrono logiko:
* Generiranje naključnih števil: `Math.floor(Math.random() * 6) + 1`.
* Vizualni zamik (`setTimeout`): Simulira "vrtenje" kock za boljšo igralno izkušnjo.
* Avtomatski izračun vsote in preverjanje konca igre.
