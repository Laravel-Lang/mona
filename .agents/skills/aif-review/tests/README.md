# Replay checks for `/aif-review +check`

```sh
npm run test:review-check
npm run test:review-check -- --report /tmp/review-check-report.json
```

The suite runs as part of `npm test` in the upstream AI Factory project. It requires installed development dependencies, Node.js 18 or later, and Git. It runs without Claude, Codex, or GitHub authentication.

The runner executes three `*.spec.yaml` files. It uses the schema loader, temporary Git sandbox setup, and assertions from the installed `@cutcode/ai-tester` package. It writes `files_staged` to the temporary repository, stages them, and checks the recorded diff against `git diff --cached`.

The runner then invokes `check-workflow.mjs run` or `advanceReview`, as described in the skill. It processes the source, runs the internal Reviewer and one validator task, parses the response, and evaluates the final gate. The Reviewer is the step where the main agent drafts the review. The replay supplies fixed responses for the Reviewer and validator. It exercises the workflow with inference disabled.

`_replay/cases.json` contains controlled Git and PR records, draft reviews, validator responses, expected statuses, and report contents. Denials, timeouts, and empty responses preserve the original findings. The workflow processes partial responses item by item. The cases cover confidence markers, multiline `Modified-text`, severity on separate lines, preservation of context gates, missing pinned evidence, freshness, and empty findings.

A PR request specifies `working_directory` explicitly. The Reviewer returns a separate `project_context` field containing a pinned excerpt or an empty string. Workflow version 3 retains the directory in both cases and adds a non-empty pinned excerpt without substituting the local description. The positive replay case checks the exact context block against `DESCRIPTION.md` at the pinned head. The negative cases verify that local context remains excluded and that missing or invalid fields are rejected before source or validator calls.

Before running the Reviewer, the host independently reads the configuration and description at the pinned head through `pr-source.mjs context`. The pinned configuration determines the path. A non-empty PR excerpt and the Reviewer record must match the independently obtained repository, path, commit, bytes, SHA-256, Git blob, and text. If the Reviewer supplies `project_context_path`, the host checks it against the independently resolved path. The Git tree can prove that the configuration is absent. An inaccessible or unsupported configuration leaves that question unresolved. If the excerpt is empty and the file is inaccessible, the workflow records the file in `unavailable_context`. A missing, blank, or duplicate `Reason` preserves the original finding and records `WARN`. The checks cover every verdict and marked item.

The replay key has the form `call_id:tool:sha256(canonical_args)`. `call_id` increases sequentially within each scenario, so two identical `TaskStatus` calls that return `pending` and `success` use different keys. The keys cover the complete argument set, including the validator prompt. The only replacement is the absolute path to the installed `pr-source.mjs`, which becomes `<references>`.

The run fails on missing, duplicate, or unused records, or when arguments differ from the recording. The replay transport runs without a callback for live tools or an SDK import. It uses local Git only to prepare and verify fixtures. The original checkout remains untouched.

The JSON report contains the review, final gate result, full call trace, assertion results, SHA-256 hashes for the skill, references, scenario, and recordings, package, Node.js, `ai-tester`, and workflow versions, and runner file hashes. The runner prints the report path at the end. `AIF_REVIEW_DEBUG=1` enables workflow diagnostics on stderr.

## Optional coupling lens

`npm run test:coupling` runs offline checks of synthetic producers and consumers,
the parser and host protocol, prepare and finish steps, integrity and source limits,
the final PR payload with both revisions, installation of each skill separately
and together across all profiles, removal of the flat workflow with its data
preserved, and the actual npm package inventory. For Kilo, the plan is in
`workflows` and the review is in `skills`. The test uses those paths. Authoring
fixtures are excluded from the published package.

The replay has separate coupling-staged and coupling-pr cases. The `lenses`
field is a compatible optional extension to workflow version 3. The new
reference and helper are included in source receipts. Disabling the lens omits
new calls and `lens` fields. Changes to SKILL/CHECK-MODE affect the Reviewer
instructions hash. The tests continue to verify validator and source payloads
and other baseline traces against the same hash contract.

The workflow validates `coupling_dependencies` before calling additional
sources. It deduplicates entries by repository, commit, and path, rejects
conflicts, and keeps inaccessible entries marked unavailable. Limits are 16
identities, 1 MiB per file, and 2 MiB total in UTF-8. The host independently
fetches available records from the pinned head or merge base of the same PR
repository, then adds them after merging the project context. Invalid data
cannot yield `PASS`. A missing external consumer leaves insufficient evidence to
confirm a defect.

The automatic-marker matrix checks the prepare and finish envelopes, the
routing instructions, and the absence of an authorized automatic fallback.
The parent skill selects the dispatch, and the tests cover its instructions.
Static planner checks run without the LLM parser. All checks run without
inference. Native runtime execution, model detection quality, and native UI
acceptance remain `NOTRUN`.

After an intentional change to the prompt or protocol, you can refresh the
recordings with these commands.

```sh
node scripts/capture-review-check.mjs --write
npm run test:review-check
```

The maintenance command runs separately from the tests and CI. It replays the explicit responses in the capture script and saves updated request hashes. Expected results are defined separately from workflow output. Review the diff before accepting recordings. Every recording comes from fixed responses. Live model variability is outside the scope.
