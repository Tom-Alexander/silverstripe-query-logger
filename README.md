
# silverstripe-query-logger
For debugging mysterious database changes

## Usage

```
RecordLogger:
  debugOperation: DELETE
  debugTableName: Member

Member:
  extensions:
    - QueryLoggingExtension
```