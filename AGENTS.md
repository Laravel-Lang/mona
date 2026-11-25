## Git Commits

- Always write Git commit messages in English, regardless of the conversation language.
- This requirement applies to both commit subjects and bodies.
- Use the `aif-commit` skill when creating commits.
- Keep it concise. Skip unnecessary details.
- Review the diff first, then compose the message.
- Use a single-line header only; do not add a body.
- Be as brief as you can.
- Aim for up to 70 characters (including any markup).
- Absolute cap: 100 characters for the header (including any markup).
- Do not end the header line with a dot.
- Enclose class names, variables, commands and attributes in backticks `like_this`. Example: Changed the type of `$foo` in class `Bar`.
- Start the header with an action verb that fits the change: added, changed, updated, fixed, improved, translated, removed, deleted, etc.
- Describe the direct edits made to the code, not their outcomes or effects.
- For class names and namespaces, omit leading and trailing backslashes.
- For changes in the `docs` folder or `README.md`, always prefix the header with `[docs]: `.
- For changes in the `src/*/source` folder, always prefix the header with `[source]: `.
- For changes in the `src/*/locales` folder, always prefix the header with the affected locale subfolder names using `[<locales>]: `, e.g., `[fr]: `, `[de,zh_CN]: `, etc.
- For changes in the `tests` folder, always prefix the header with `[tests]: `.
- For changes in the `.agents` folder or `AGENTS.md`, always prefix the header with `[agents]: `.
- For changes in all other folders, always prefix the header with `[app]: `.

## Documentation

- Update the documentation for every configured documentation locale as part of each code change.
- In the current version of the documentation, omit version constraints from dependency installation commands unless the user explicitly requests them. For example, use `composer require laravel-lang/models` instead of `composer require "laravel-lang/models:20.*"`.

## Testing

- When writing tests, verify the feature's observable behavior through its normal entry points.
- Unit tests are optional.

## Task Coordination and Acceptance

- Study the task specification and requirements before starting work.
- Make a plan suited to the task and delegate according to [Subagents](#subagents).
- Review each delegated result against the specification and plan.
- Send defects, inconsistencies, and deviations back to the assigned subagent for correction.
- Accept the work only after verifying that all identified issues have been resolved and the requirements are met.

## Subagents

- Delegate substantial, independent work that can run in parallel when it is expected to materially improve quality
  or reduce completion time. Handle short tasks, routine exploration, sequential work, and work that depends heavily
  on the current agent's context directly.
- Give each subagent a clear scope, the relevant context, and an expected deliverable. Subagents may delegate further
  only if their own work meets the same threshold.
- Delegate verification only when an independent review materially improves reliability on complex or high-risk work.
- Use the configured subagent defaults. When neither the model nor the reasoning effort is configured, Codex inherits
  both from the parent agent. Override these settings only when the subtask clearly requires it. Explicitly select an
  Astra model for a subagent only at the user's request.
- Use clear wording and proper spacing in subagent messages and final responses.
