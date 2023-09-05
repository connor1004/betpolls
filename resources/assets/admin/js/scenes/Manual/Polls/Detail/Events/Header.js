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
              <div style={{ width: '60px' }}>
                <TextStyle variation="strong">ID</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">Name</TextStyle>
            </Stack.Item>
            <Stack.Item fill>
              <TextStyle variation="strong">Name ES</TextStyle>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '100px' }}>
                <TextStyle variation="strong">Published</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '140px' }}>
                <TextStyle variation="strong">Action</TextStyle>
              </div>
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
