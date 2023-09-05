const OptionsHelper = {
  getOptions(list, valueKey, labelKey, emptyOption = false) {
    let ret;
    if (list) {
      ret = list.map(value => ({
        value: `${value[valueKey]}`,
        label: value[labelKey]
      }));
      if (emptyOption) {
        ret.unshift(emptyOption);
      }
    } else {
      ret = [];
    }
    return ret;
  },
  getCustomOptions(list, getValue, getLabel, emptyOption = false) {
    let ret;
    if (list) {
      ret = list.map(value => ({
        value: getValue(value),
        label: getLabel(value)
      }));
      if (emptyOption) {
        ret.unshift(emptyOption);
      }
    } else {
      ret = [];
    }
    return ret;
  },
  getOption(value, options, defaultOption = null) {
    for (let i = 0, ni = options.length; i < ni; i++) {
      const option = options[i];
      if (`${value}` === option.value) {
        return option;
      }
    }
    return defaultOption;
  },
  getItem(value, field, list, defaultItem = null) {
    for (let i = 0, ni = list.length; i < ni; i++) {
      const item = list[i];
      if (`${value}` === `${item[field]}`) {
        return item;
      }
    }
    return defaultItem;
  }
};

export default OptionsHelper;
