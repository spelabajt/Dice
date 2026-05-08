# 🦋 Butterfly: Digital Dice Experience

**Butterfly Sanctuary** is not just a simple dice game; it is a web application that blends "Glassmorphism" aesthetics with a fluid user experience. The project is designed as a lighthearted social game where players (butterflies) compete to collect points through strategically set dice rolls.

---

## Key Features

### 1. Dynamic Player Management
The system allows for adding up to 6 players. Each player card is equipped with a unique identifier, enabling the removal of an individual without affecting other data stored in the session.

### 2. Customizable Gameplay Mechanics
The fate of the rolls is determined before the game begins:
* **Number of Dice:** Choose between rolling 1, 2, or 3 dice simultaneously.
* **Repetitions (Reps):** Determines how many times each player can roll the dice (1–10 rounds).

### 3. "Glassmorphism" Design
The user interface utilizes modern CSS techniques:
* `backdrop-filter: blur(20px)` for a frosted glass effect.
* Neon purple and blue shadows (`box-shadow`) for depth.
* A responsive Flexbox grid system.

---

## Technical Insights

### Session Handling
The game relies heavily on PHP sessions (`$_SESSION`). Reset logic is implemented on two levels:
1. **Hard Reset:** Upon entering via `index.html`, `session_destroy()` is triggered to ensure old game data does not interfere with new players.
2. **Soft Reset:** The "Play Again" button at the end of the game clears only the player array while preserving system settings if necessary.

### JavaScript Dice Logic
Instead of constant page refreshes, `s2.php` utilizes asynchronous logic:
* Random number generation: `Math.floor(Math.random() * 6) + 1`.
* Visual delay (`setTimeout`): Simulates the "rolling" of dice for a more immersive gaming experience.
* Automatic sum calculation and end-game verification.
