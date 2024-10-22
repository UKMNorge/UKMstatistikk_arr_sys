// utils/colors.ts
export function getRandomColor(transparency = 1, position: number): string {
    // Colors array
    const Colours = [
        `rgba(51, 161, 255, ${transparency})`, // Bright Sky Blue
        `rgba(40, 167, 69, ${transparency})`, // Medium Green
        `rgba(255, 193, 7, ${transparency})`, // Amber Yellow
        `rgba(255, 87, 51, ${transparency})`, // Vivid Red-Orange
        `rgba(111, 66, 193, ${transparency})`, // Medium Purple
        `rgba(232, 62, 140, ${transparency})`, // Vivid Pink
        `rgba(32, 201, 151, ${transparency})`, // Bright Mint Green
        `rgba(253, 126, 20, ${transparency})`, // Orange
        `rgba(102, 16, 242, ${transparency})`, // Indigo
        `rgba(23, 162, 184, ${transparency})`, // Cyan
        `rgba(255, 99, 71, ${transparency})`, // Tomato Red
        `rgba(139, 69, 19, ${transparency})`, // Saddle Brown
        `rgba(0, 206, 209, ${transparency})`, // Dark Turquoise
        `rgba(201, 255, 67, ${transparency})`, // Lime Green
        `rgba(70, 130, 180, ${transparency})`, // Steel Blue
        `rgba(220, 20, 60, ${transparency})`, // Crimson
        `rgba(176, 229, 124, ${transparency})`, // Light Green
        `rgba(255, 105, 180, ${transparency})`, // Hot Pink
        `rgba(135, 206, 235, ${transparency})`, // Sky Blue
        `rgba(127, 255, 0, ${transparency})`, // Chartreuse
    ];

    // Return the color directly if the position is within bounds
    if (position < Colours.length) {
        return Colours[position];
    }

    // Return a random color if the position is out of bounds
    const hues = [
        0,    // Red
        120,  // Green
        240,   // Orange
        60,  // Cyan
        30,  // Blue
        180   // Magenta
    ];

    // Randomly pick one of the pure hues
    const hue = hues[Math.floor(Math.random() * hues.length)];

    // Set saturation to 100% for vivid colors
    const saturation = 100;

    // Slightly lighter lightness (range: [60, 80])
    const lightness = Math.floor(Math.random() * 20) + 60;

    return `hsla(${hue}, ${saturation}%, ${lightness}%, ${transparency})`;
}