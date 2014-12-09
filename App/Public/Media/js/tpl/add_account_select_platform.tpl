<div class="mid-batch pr l">
    <div class="tubiao">
        <ul class="addpp">
            {{~ it :value:index}}
            <li>
                <a class="a4" href="javascript:void(0)" data-pid="{{!value.pid}}" data-name="{{!value.platformName}}">
                <img src="{{!value.platformIcon}}" />
                <em>{{!value.platformName}}</em>
                </a>
            </li>
            {{~}}
        </ul>
    </div>
</div>
