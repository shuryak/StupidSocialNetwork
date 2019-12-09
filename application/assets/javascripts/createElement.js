function createElement(elementName, props = {}, children) {
  const element = document.createElement(elementName);

  if(props.on) {
    for(const listener in props.on) {
      element.addEventListener(listener, props.on[listener]);
    }
    
    delete props.on;
  }

  for(const propName in props) {
    if(propName in element) element[propName] = props[propName];
    else element.setAttribute(propName, props[propName]);
  }

  if(Array.isArray(children)) {
    for(const child of children) {
      if(child instanceof Node) element.appendChild(child);
      else element.append(document.createTextNode(child));
    }
  } else if(children != null) {
    element.innerHTML = children;
  }

  return element;
}