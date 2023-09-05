import React, { Component } from 'react';
import {
  Card, Stack, ResourceList, TextStyle
} from '@shopify/polaris';

class Header extends Component {
  render() {
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '50px' }}>ID</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '240px' }}>Title</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">
                <div style={{ width: '160px' }}>Slug</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '100px' }}>Created at</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '100px' }}>Updated at</div>
              </TextStyle>
            </Stack.Item>
            <Stack.Item>
              <TextStyle variation="strong">
                <div style={{ width: '100px' }} />
              </TextStyle>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
