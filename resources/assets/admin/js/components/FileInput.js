/* eslint-disable no-undef */
import React, { Component } from 'react';
import {
  TextField, Button
} from '@shopify/polaris';

class FileInput extends Component {
  constructor(props) {
    super(props);
    this.state = {
      value: '' || props.value
    };
    this.handleBrowse = this.handleBrowse.bind(this);
    this.handleChange = this.handleChange.bind(this);
  }

  componentWillReceiveProps(props) {
    if (props.value !== this.props.value) {
      this.setState({
        value: props.value
      });
    }
  }

  handleChange(value) {
    this.setState({
      value
    });
    this.props.onChange(value);
  }

  handleBrowse() {
    CKFinder.modal({
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: (finder) => {
        finder.on('files:choose', (evt) => {
          const file = evt.data.files.first();
          const value = file.getUrl();
          this.setState({
            value
          });
          this.props.onChange(value);
        });
      }
    });
  }

  render() {
    const {
      value
    } = this.state;

    const {
      value: valueIgnore, onChange, ...otherProps
    } = this.props;

    return (
      <div style={{ display: 'flex', alignItems: 'flex-end' }}>
        <div style={{ flex: 1 }}>
          <TextField
            value={value}
            onChange={this.handleChange}
            {...otherProps}
          />
        </div>
        <div>
          <Button
            primary
            onClick={this.handleBrowse}
            icon="add"
          >
            Browse
          </Button>
        </div>
      </div>
    );
  }
}

FileInput.defaultProps = {
  onChange: () => {}
};

export default FileInput;
