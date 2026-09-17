# v5.0.0

- Support Filament v5, Livewire 4 and Laravel 12 / 13 (PHP 8.2+).
- Register the Trigger, Action and Workflow Log resources on the panel next to Workflows, grouped under "Workflows".
- Workflow create page, active toggle on the workflow form and trigger / run counts on the list.
- Model, event and webhook triggers now run the attached actions of every active workflow and write a Workflow Log row for each run (the updated / deleted listeners used to crash, logs were only written for created and webhook runs).
- Trigger filters work: "only this record" (`model_id`) and the `where` / `is` / `value` condition.
- Event triggers: events registered with `FilamentWorkflows::event()` are now listened to.
- `FilamentWorkflows::action([...])` registers every action of the array (it used to call an undefined method).
- `InteractsWithWorkflow` looks triggers up when the model event fires, not while the model boots, so it is safe before the workflow tables are migrated.
- Only classes extending `Services\Abstracts\Action` are executed; a failing action is logged and does not stop the other actions.
- Webhook: accepts GET and POST, the token can be sent as `token` or the `X-Workflow-Token` header, compared in constant time, the endpoint stays closed while `FILAMENT_WORKFLOWS_WEBHOOK_TOKEN` is empty, the token is not stored in the logs, and the route is throttled.
- Model / webhook events are no longer broadcast on the public `workflow` channel.
- Builder page rebuilt for Filament v5 (actions are passed record ids instead of serialized models); "Edit Trigger" updates the trigger itself; "Action Settings" only shows when the action class defines a form.
- Fix the default action icon (`heroicon-s-arrows-up-down`).
- Model factories for Workflow, Trigger, Action and Workflow Log (the models used `HasFactory` without shipping factories).
- Pest test suite.

# V1.0.0

First release of the package
