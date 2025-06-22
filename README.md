# Art Bloat Trimmer

Плагин отключения мусора в админке

Поддерживаются плагины:

* WooCommerce - возможно отключать все новые функции и разделы: Маркетинг, Аналитика, Бординг и тд относящиеся к WooCommerce Admin
* SEO Rank Math - возможно отключть рекламу в админке, комментарии и тд

## CLI команды

### Unified flush command

`wp abt flush --type=<type> [--select] [--batch=<size>]`

```
OPTIONS
 --type=<type>
What to flush. Possible values: actions, logs, notes, all
 ---
 default: all
 options:
  - actions
  - logs
  - notes
  - all

[--select]
Dry-run mode (show what will be deleted)
default: false

[--batch=<size>]
Batch size for processing (0 for no batching)
default: 0

EXAMPLES
# Flush all type
$ wp abt flush
Success: Flush completed.

# Preview scheduled actions to be deleted
$ wp abt flush --type=actions --select
```

[Changelog](https://github.com/artikus11/art-bloat-trimmer/blob/master/CHANGELOG.md)
