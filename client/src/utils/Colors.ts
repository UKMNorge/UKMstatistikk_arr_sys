// utils/colors.ts
export function getRandomColor(transparency = 1, position: number): string {
    // Colors array
    const Colours = [
        '#FF5733', // Vivid Red-Orange
        '#33A1FF', // Bright Sky Blue
        '#28A745', // Medium Green
        '#FFC107', // Amber Yellow
        '#6F42C1', // Medium Purple
        '#E83E8C', // Vivid Pink
        '#20C997', // Bright Mint Green
        '#FD7E14', // Orange
        '#6610F2', // Indigo
        '#17A2B8', // Cyan
        '#FF6347', // Tomato Red
        '#8B4513', // Saddle Brown
        '#00CED1', // Dark Turquoise
        '#C9FF43', // 
        '#4682B4', // Steel Blue
        '#DC143C', // Crimson
        '#B0E57C', // Light Green
        '#FF69B4', // Hot Pink
        '#87CEEB', // Sky Blue
        '#7FFF00', // Chartreuse
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