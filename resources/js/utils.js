const parseKeys = (obj) =>
  Object.keys(obj).reduce((acc, key) => {
    const value = obj[key];

    const result = convert(value);

    return { ...acc, [key]: result };
  }, {});

const convert = (value) => {
  if (typeof value === "object" && value !== null) {
    if (Array.isArray(value)) {
      return convertArray(value);
    } else {
      return parseKeys(value);
    }
  }

  switch(value) {
    case "true":
      return true;
    case "false":
      return false;
    case "null":
      return null;
    case "undefined":
      return undefined;
    default:
      if (
        !isNaN(parseFloat(value)) &&
      value === parseFloat(value).toString()
    ) {
      return parseFloat(value);
    } else {
        return value;
    }
  }

};

const convertArray = (a) => a.map(el => convert(el));


export default parseKeys;
