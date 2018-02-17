# Consul.conf

## Customization :art:

Customizing both dashboards and fields is a matter of setting proper JSON value under specified key in Consul key-value storage, [see what are the different ways to do so](CONSUL.md#storing-values-in-key-value-storage).

Combining all examples on this page lets you create dashboard as on below screenshot, you can also install them locally using [automated install script](../README.md#install-examples).

![](https://user-images.githubusercontent.com/5011490/36344756-3725fa22-141f-11e8-8b08-1674c7d0d002.png)

### Customizing dashboard

Dashboard customization can be achieved by setting proper JSON value in Consul key-value storage. If you want to customize dashboard having `project/` base path, edit `project/.dashboard/.config` key. The same rule applies for nested paths, for `project/sub/path/` base path you have to edit `project/sub/path/.dashboard/.config`.

Following keys are recognized inside the JSON object:

| Key | Type | Default | Notes |
| --- | --- | --- | --- |
| name | `string` | `"Dashboard"` | |
| description | `string` | `""` | Markdown flavoured |
| color | [`color` &#8674;](https://mdbootstrap.com/css/colors/) | `"stylish-color"` | any available color |
| icon | [`icon` &#8674;](https://mdbootstrap.com/content/icons-list/) | `"dashboard"` | any available icon |
| hidden | `bool` | `false` | |
| hide_unconfigured | `bool` | `false` | whether to hide fields which have not been configured |

```bash
# create customized dashboard under "project/" base path
curl -X PUT -d \
    '{
        "name": "Project dashboard",
        "description": "Change **production** settings on this dashboard.\n\nVisit https://www.project.com/ to monitor your changes in action.",
        "color": "deep-orange",
        "icon": "cogs",
        "hidden": false,
        "hide_unconfigured": false
    }' \
    http://localhost:8500/v1/kv/project/.dashboard/.config
```

### Customizing field

Same as with dashboards, field customization can be done by setting proper JSON value in Consul key-value storage. Assuming your dashboard's base path is `project/`, if you want to customize field having `project/key` path, edit `project/.dashboard/key` key. The same rule applies for nested keys, for `project/sub/path/key` you have to edit `project/.dashboard/sub/path/key`.

> Field will not be visible in web UI if it is only configured inside `.dashboard/` but the origin key itself does not exist.

Following keys are recognized inside the JSON object:

| Key | Type | Default | Notes |
| --- | --- | --- | --- |
| name | `string` | field's key path | |
| type | <code>string ("text"&#124;"select"&#124;"checkbox")</code> | `"text"` | |
| options | `array` | `null` | valid only for `select` field type |
| description | `string` | `""` | Markdown flavoured |
| default | <code>string&#124;bool</code> | `null` | default value displayed in field's hint |
| color | [`color` &#8674;](https://mdbootstrap.com/css/colors/) | inherited from dashboard | any available text color |
| icon | [`icon` &#8674;](https://mdbootstrap.com/content/icons-list/) | `null` | any available icon |
| readonly | `bool` | `false` | |
| hidden | `bool` | `false` | |

#### Text

Text input allows any value, it is also a default type when no `type` is specified or has invalid value.

```bash
# create customized text field under "project/text-key" path
curl -X PUT -d \
    '{
        "name": "Example text key",
        "type": "text",
        "description": "Any string value is accepted here.",
        "default": "value",
        "color": "red",
        "icon": "heart",
        "readonly": false,
        "hidden": false
    }' \
    http://localhost:8500/v1/kv/project/.dashboard/text-key

# set the value of "project/text-key"
curl -X PUT -d 'value' \
    http://localhost:8500/v1/kv/project/text-key
```

#### Select

Select input allows a value from pre-defined set of options. Set `type` to `select` and place available options inside `options` array.

```bash
# create customized select field under "project/select-key" path
curl -X PUT -d \
    '{
        "name": "Example option key",
        "type": "select",
        "options": ["option1", "option2", "option3"],
        "description": "See https://project.com/docs#select-key for details.",
        "default": "option1",
        "color": "green",
        "icon": "leaf",
        "readonly": false,
        "hidden": false
    }' \
    http://localhost:8500/v1/kv/project/.dashboard/select-key

# set the value of "project/select-key"
curl -X PUT -d 'option2' \
    http://localhost:8500/v1/kv/project/select-key
```

#### Checkbox

Checkbox input allows only `true` or `false` states. Value stored when saving dashboard is literally either `true` or `false`. Enable by setting value of `type` to `checkbox`.

```bash
# create customized checkbox field under "project/checkbox-key" path
curl -X PUT -d \
    '{
        "name": "Example checkbox key",
        "type": "checkbox",
        "description": "**Important key** for system stability.",
        "default": true,
        "color": "blue",
        "icon": "plane",
        "readonly": true,
        "hidden": false
    }' \
    http://localhost:8500/v1/kv/project/.dashboard/checkbox-key

# set the value of "project/checkbox-key"
curl -X PUT -d 'false' \
    http://localhost:8500/v1/kv/project/checkbox-key
```