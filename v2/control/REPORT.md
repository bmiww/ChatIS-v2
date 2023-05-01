# report.php - ChatIS report API

## Control panel

### Overlay states

| State     | Timeout | Can
| --------- | ------- | ---
| `online`  | `10s`   | 
| `offline` | `7d`    | 
| `dead`    | `90d`   | 

After `dead` the entry is archived

Overlay states
An overlay is considered online if we got a report in the last 10 seconds
An overlay is considered

## Request structure





Accept params:
- channel: name of the channel showed in overlay, lowercase if needed
- session: unique id of the current local ChatIS session, generate on each ChatIS reload
- startTimestamp: timestamp of the moment ChatIS fully loaded (used for uptime calc and etc)
- config: somehow the whole config (query string of ChatIS?)
-

Await changes map response if any present

## H