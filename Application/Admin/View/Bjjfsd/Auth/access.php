<style media="screen">
.rules dl {
    background-color: #FFF;
    margin: 20px auto;
}
</style>

<form action="" method="post">
    <div class="rules">
        <volist name="list" id="value">
            <dl>
                <dt><input class="check-all" type="checkbox" name="rules[]" value="{$value['id']}">{$value['title']}</dt>
                <dt>
                <volist name="value['child']" id="val">
                    <dd>
                        <dl>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            <input class="check-row" type="checkbox" name="rules[]" value="{$val['id']}">{$val['title']}
                        </label>
                    </dl>
                        <dl>&nbsp;&nbsp;&nbsp;&nbsp;
                        <volist name="val['child']" id="v">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="rules[]" value="{$v['id']}">
                            {$v['title']}
                        </volist>
                    </dl>
                    </dd>
                </volist>
                </dt>
            </dl>
        </volist>
    </div>
     <input type="hidden" name="id" value="{$info['id']|default=0}">
     <input type="submit" class="tjanniu cr" value="提 交" /><input type="reset" class="czanniu cr" value="重 置" />
</form>

<script type="text/javascript">
$(function(){
    var rules = [{$info['rules']}];
        $('input:checkbox').each(function(){
            if( $.inArray( parseInt(this.value, 10), rules )>-1 ){
                $(this).attr('checked',true);
            }
        });
    $(".check-all").change(function(){
        $(this).parents("dl").find("input").attr('checked', this.checked);
    });
    $(".check-row").change(function(){
        $(this).parents("dd").find("input").attr('checked', this.checked);
    });
})
</script>
