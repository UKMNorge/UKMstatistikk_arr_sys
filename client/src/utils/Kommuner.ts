type LocationObject = { [key: string]: string };
type DataObject = { [year: number]: LocationObject };

export function summarizeData(data: DataObject): { summary: { [range: string]: string }[], brukteKommuner: string[] } {
    let brukteKommuner: string[] = [];
    let summary: { [range: string]: string }[] = [];

    for (const year in data) {
        for (const kommune in data[year]) {
            if (!brukteKommuner.includes(kommune)) {
                brukteKommuner.push(kommune);
            }
        }
    }

    let startYear: number | null = null;
    let endYear: number | null = null;
    let lastValue: string | null = null;

    // Helper function to create a readable string from the LocationObject
    function formatLocation(location: LocationObject): string {
        return Object.keys(location).map(key => location[key]).join(", ");
    }

    // Iterate over each year in the data object
    for (const yearString in data) {
        const year = parseInt(yearString, 10); // Convert year string to number
        const currentValue = formatLocation(data[year]);

        if (lastValue === null) {
            // Initialize for the first year
            startYear = year;
        } else if (currentValue !== lastValue) {
            // If value changes, finalize the previous range
            endYear = year - 1;
            if (lastValue) {  // Ensure lastValue is not null
                summary.push({ [`${startYear}-${endYear}`]: lastValue });
            }
            startYear = year; // Start a new range
        }

        // Update for the next iteration
        lastValue = currentValue;
    }

    // Add the final range
    endYear = parseInt(Object.keys(data).pop() || "", 10);
    if (startYear !== null && endYear !== null && lastValue) {
        summary.push({ [`${startYear}-${endYear}`]: lastValue });
    }

    return { summary, brukteKommuner };
}
