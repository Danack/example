# esprintf

sprintf mixed with zend escaper



## Usage

```
    $string = "<td class=':css_class'>:html_message</td>";
    $params = [
        ':css_class' => 'warning',
        :html_message' => 'foo bar'
    ];
     
    echo esprintf($string, $params);
     
```