module.exports = {
  extends: ["@commitlint/config-conventional"],
  rules: {
    // Prose-style commit bodies routinely run past the preset's 100-char
    // cap; unwrapped long lines aren't a real defect, so the cap is off.
    "body-max-line-length": [0, "always", Infinity],
    // The parser reclassifies body lines that carry issue refs (e.g. "#9")
    // as footer tokens, so the footer cap fires the same false positive on
    // long prose lines. Real footers (Closes #N, trailers) stay short, so
    // this cap is off too.
    "footer-max-line-length": [0, "always", Infinity],
  },
};
