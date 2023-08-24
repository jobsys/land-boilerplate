由于 Laravel 原生的 Notification 会以该类名为 `type` 字段的值， 所以我们以类型的后缀来区分不同类型的通知：

| 类型 | 后缀           | 说明 |
|----|--------------|----|
| 待办 | Todo         |    |
| 通知 | Notification |    |
