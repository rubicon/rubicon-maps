module.exports = {
  extends: ["@commitlint/config-conventional"],
  rules: {
    // Prose-style commit bodies routinely run past the preset's 100-char
    // cap; unwrapped long lines aren't a real defect, so the cap is off.
    "body-max-line-length": [0, "always", Infinity],
  },
};
