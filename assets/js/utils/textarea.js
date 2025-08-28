// Estimate the visible line count for a textarea's content 
export function predictLines(textarea) {
    const styles = getComputedStyle(textarea);
    const fontSize = parseFloat(styles.fontSize);
    const width = textarea.clientWidth;
  
    if (!fontSize || !width || isNaN(fontSize)) return 1;
    
    const charsPerLine = Math.floor(width / (fontSize * 0.55));
    const lines = textarea.value.split("\n").reduce((total, line) => {
      const wrappedLines = Math.ceil(line.length / charsPerLine);
      return total + wrappedLines;
    }, 0);
  
    return Math.min(3, Math.max(1, lines));
}