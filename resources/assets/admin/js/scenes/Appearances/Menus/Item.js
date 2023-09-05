import React, { Component } from 'react';
import {
  Icon,
  Button,
  ButtonGroup,
  DisplayText
} from '@shopify/polaris';

class Item extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSelect = this.handleSelect.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
  }

  handleSelect() {
    const {
      onSelect, data
    } = this.props;
    onSelect(data.index);
  }

  async handleDelete() {
    const {
      data, onDelete
    } = this.props;
    await onDelete(data.index, this);
  }

  render() {
    const {
      data
    } = this.props;
    const {
      path, connectDragSource, connectDropTarget, connectDragPreview,
      isDragging, isClosestDragging
    } = data;
    const itemStyle = {
      border: '1px solid #eee',
      padding: '10px',
      borderRadius: '3px',
      marginBottom: '8px'
    };
    const muteStyle = { opacity: 0.3 };

    const style = {
      ...itemStyle,
      ...(isDragging || isClosestDragging ? muteStyle : null),
      marginLeft: path.length * 30
    };

    return (
      connectDragSource(connectDragPreview(connectDropTarget(
        <div style={style}>
          <div
            style={{
              display: 'flex'
            }}
          >
            <div style={{ flex: 1 }}>
              <DisplayText size="small">{data.title}</DisplayText>
              <div>{data.url}</div>
            </div>
            <div style={{paddingTop: '15px', width: '40px'}}>
              {data.parent_id == 0 && data.top_menu ? <Icon source='checkmark' /> : ''}
            </div>
            <div style={{paddingTop: '15px', width: '40px'}}>
              {data.burger_menu ? <Icon source='view' /> : ''}
            </div>
            <ButtonGroup>
              <Button
                button
                primary
                onClick={this.handleSelect}
                icon="products"
              />
              <Button
                button
                destructive
                onClick={this.handleDelete}
                icon="delete"
              />
            </ButtonGroup>
          </div>
        </div>
      )))
    );
  }
}

Item.defaultProps = {
  active: false,
  onSelect: () => {},
  onDelete: () => {},
  data: {}
};

export default Item;
